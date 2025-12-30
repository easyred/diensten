<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://api.whatsapp.com');
        $this->apiKey = config('services.whatsapp.api_key');
    }

    /**
     * Send a WhatsApp message
     */
    public function sendMessage($phoneNumber, $message)
    {
        try {
            $botUrl = config('services.wa_bot.url', 'http://127.0.0.1:3000');
            $response = Http::post($botUrl . '/send-message', [
                'number' => $phoneNumber,
                'message' => $message
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $phoneNumber,
                    'response' => $response->json(),
                    'timestamp' => now()
                ]);
                return true;
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'to' => $phoneNumber,
                    'response' => $response->body(),
                    'status' => $response->status(),
                    'timestamp' => now()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp message', [
                'to' => $phoneNumber,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
            return false;
        }
    }

    /**
     * Send welcome message to new user
     */
    public function sendWelcomeMessage($user, $hasActiveSubscription = false)
    {
        if (empty($user->whatsapp_number)) {
            Log::warning('Cannot send welcome message: user has no WhatsApp number', [
                'user_id' => $user->id ?? null,
                'user_email' => $user->email ?? null
            ]);
            return false;
        }

        try {
            $message = $this->buildWelcomeMessage($user, $hasActiveSubscription);
            return $this->sendMessage($user->whatsapp_number, $message);
        } catch (\Exception $e) {
            Log::error('Error building or sending welcome message', [
                'user_id' => $user->id ?? null,
                'whatsapp_number' => $user->whatsapp_number ?? null,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Build welcome message content
     */
    private function buildWelcomeMessage($user, $hasActiveSubscription)
    {
        $message = "🎉 *Welcome to diensten.pro!*\n\n";
        $message .= "Hello {$user->full_name}!\n";
        $message .= "Welcome to diensten.pro! We're excited to have you on board.\n\n";
        
        $message .= "*Your Account Details:*\n";
        $message .= "• Role: " . ucfirst($user->role) . "\n";
        $message .= "• Email: {$user->email}\n";
        
        if ($user->address) {
            $message .= "• Address: {$user->address}\n";
        }
        
        if ($user->company_name) {
            $message .= "• Company: {$user->company_name}\n";
        }
        
        if (!$hasActiveSubscription) {
            $message .= "\n🚀 *Get Started with Our Services*\n\n";
            $message .= "Visit our website to view subscription packages: " . url('/#pricing') . "\n\n";
        } else {
            $message .= "\n🎉 *You're All Set!*\n\n";
            $message .= "Visit your dashboard: " . url('/dashboard') . "\n\n";
        }
        
        $message .= "Best regards,\nThe diensten.pro Team";

        return $message;
    }
}

