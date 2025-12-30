<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class OtpService
{
    protected $whatsappService;
    protected $otpExpiryMinutes = 10;
    protected $maxAttempts = 5;
    protected $maxRequestsPerHour = 3;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Generate and send OTP to WhatsApp number
     */
    public function generateAndSendOtp(string $whatsappNumber, ?string $ipAddress = null): array
    {
        $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);

        $rateLimitKey = 'otp_requests:' . $whatsappNumber;
        if (RateLimiter::tooManyAttempts($rateLimitKey, $this->maxRequestsPerHour)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return [
                'success' => false,
                'message' => 'Te veel verzoeken. Probeer het over ' . ceil($seconds / 60) . ' minuten opnieuw.',
                'retry_after' => $seconds
            ];
        }

        // For testing: Skip actual WhatsApp sending and just create OTP
        // TODO: Remove this bypass later - it's for testing only
        // Check if number matches test patterns (handles both 10 and 11 digit versions)
        $testPatterns = ['3247012345', '3247098765', '3247011111'];
        $isTestNumber = false;
        foreach ($testPatterns as $pattern) {
            if (strpos($whatsappNumber, $pattern) === 0) {
                $isTestNumber = true;
                break;
            }
        }

        Otp::where('whatsapp_number', $whatsappNumber)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->update(['verified_at' => now()]);

        // For test numbers, always use 123456. For others, generate random code
        $otpCode = $isTestNumber ? '123456' : str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        $otp = Otp::create([
            'whatsapp_number' => $whatsappNumber,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes($this->otpExpiryMinutes),
            'ip_address' => $ipAddress,
        ]);

        if ($isTestNumber) {
            // Skip WhatsApp sending for test users - always succeed
            RateLimiter::hit($rateLimitKey, 3600);

            Log::info('OTP generated for test user (WhatsApp sending skipped)', [
                'whatsapp_number' => $whatsappNumber,
                'otp_id' => $otp->id,
                'otp_code' => $otpCode,
                'expires_at' => $otp->expires_at
            ]);

            return [
                'success' => true,
                'message' => 'OTP is verzonden naar uw WhatsApp.',
                'otp_id' => $otp->id
            ];
        }

        // Normal flow: Send via WhatsApp
        $message = "🔐 *diensten.pro Login Code*\n\n";
        $message .= "Uw verificatiecode is: *{$otpCode}*\n\n";
        $message .= "Deze code is 10 minuten geldig.\n";
        $message .= "Deel deze code nooit met anderen.\n\n";
        $message .= "Als u deze code niet heeft aangevraagd, negeer dit bericht.";

        $sent = $this->whatsappService->sendMessage($whatsappNumber, $message);

        if ($sent) {
            RateLimiter::hit($rateLimitKey, 3600);

            Log::info('OTP generated and sent', [
                'whatsapp_number' => $whatsappNumber,
                'otp_id' => $otp->id,
                'expires_at' => $otp->expires_at
            ]);

            return [
                'success' => true,
                'message' => 'OTP is verzonden naar uw WhatsApp.',
                'otp_id' => $otp->id
            ];
        } else {
            $otp->delete();

            Log::error('Failed to send OTP via WhatsApp', [
                'whatsapp_number' => $whatsappNumber
            ]);

            return [
                'success' => false,
                'message' => 'Kon OTP niet verzenden. Controleer uw WhatsApp-nummer en probeer het opnieuw.'
            ];
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(string $whatsappNumber, string $otpCode, ?string $ipAddress = null): array
    {
        $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);

        // For testing: Bypass validation for test numbers with code 123456
        // TODO: Remove this bypass later - it's for testing only
        $testPatterns = ['3247012345', '3247098765', '3247011111'];
        $isTestNumber = false;
        foreach ($testPatterns as $pattern) {
            if (strpos($whatsappNumber, $pattern) === 0) {
                $isTestNumber = true;
                break;
            }
        }

        if ($isTestNumber && $otpCode === '123456') {
            // For test numbers, accept 123456 without checking database
            $user = User::where('whatsapp_number', $whatsappNumber)
                ->orWhere('phone', $whatsappNumber)
                ->first();

            if (!$user) {
                $registerUrl = route('client.register');
                return [
                    'success' => false,
                    'message' => 'Geen account gevonden met dit WhatsApp-nummer. <a href="' . $registerUrl . '" class="font-bold underline">Registreer</a> eerst een account.',
                    'message_html' => true
                ];
            }

            // Mark any existing OTPs as verified
            Otp::where('whatsapp_number', $whatsappNumber)
                ->whereNull('verified_at')
                ->update(['verified_at' => now()]);

            Log::info('OTP verified for test user (bypass)', [
                'whatsapp_number' => $whatsappNumber,
                'user_id' => $user->id
            ]);

            return [
                'success' => true,
                'message' => 'OTP geverifieerd.',
                'user' => $user
            ];
        }

        $otp = Otp::where('whatsapp_number', $whatsappNumber)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'Geen geldige OTP gevonden. Vraag een nieuwe code aan.'
            ];
        }

        if ($otp->isExpired()) {
            return [
                'success' => false,
                'message' => 'De OTP is verlopen. Vraag een nieuwe code aan.'
            ];
        }

        if ($otp->hasExceededMaxAttempts($this->maxAttempts)) {
            return [
                'success' => false,
                'message' => 'Te veel foutieve pogingen. Vraag een nieuwe code aan.'
            ];
        }

        if ($otp->otp_code !== $otpCode) {
            $otp->incrementAttempts();
            $remainingAttempts = $this->maxAttempts - $otp->attempts;
            
            return [
                'success' => false,
                'message' => 'Ongeldige OTP-code.' . ($remainingAttempts > 0 ? " U heeft nog {$remainingAttempts} pogingen over." : ' Vraag een nieuwe code aan.'),
                'remaining_attempts' => $remainingAttempts
            ];
        }

        $otp->markAsVerified();

        $user = User::where('whatsapp_number', $whatsappNumber)
            ->orWhere('phone', $whatsappNumber)
            ->first();

        if (!$user) {
            $registerUrl = route('client.register');
            return [
                'success' => false,
                'message' => 'Geen account gevonden met dit WhatsApp-nummer. <a href="' . $registerUrl . '" class="font-bold underline">Registreer</a> eerst een account.',
                'message_html' => true
            ];
        }

        Log::info('OTP verified successfully', [
            'whatsapp_number' => $whatsappNumber,
            'user_id' => $user->id,
            'otp_id' => $otp->id
        ]);

        return [
            'success' => true,
            'message' => 'OTP geverifieerd.',
            'user' => $user
        ];
    }

    /**
     * Clean up expired OTPs
     */
    public function cleanupExpiredOtps(): void
    {
        $deleted = Otp::where('expires_at', '<', now()->subDays(1))->delete();
        Log::info('Cleaned up expired OTPs', ['deleted_count' => $deleted]);
    }
}

