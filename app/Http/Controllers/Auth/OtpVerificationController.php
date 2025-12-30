<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the OTP verification form
     */
    public function show(Request $request)
    {
        $whatsappNumber = $request->session()->get('whatsapp_login_number');
        
        if (!$whatsappNumber) {
            return redirect()->route('login')
                ->withErrors(['whatsapp_number' => 'Vraag eerst een OTP-code aan.']);
        }

        return view('auth.otp-verify', [
            'whatsapp_number' => $whatsappNumber
        ]);
    }

    /**
     * Verify OTP and log in user
     */
    public function verify(Request $request): RedirectResponse
    {
        $whatsappNumber = $request->session()->get('whatsapp_login_number');
        
        if (!$whatsappNumber) {
            return redirect()->route('login')
                ->withErrors(['whatsapp_number' => 'Sessie verlopen. Vraag een nieuwe OTP-code aan.']);
        }

        $validator = $request->validate([
            'otp_code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp_code.required' => 'OTP-code is verplicht.',
            'otp_code.size' => 'OTP-code moet 6 cijfers zijn.',
            'otp_code.regex' => 'OTP-code moet alleen cijfers bevatten.',
        ]);

        // Verify OTP
        $result = $this->otpService->verifyOtp(
            $whatsappNumber,
            $request->otp_code,
            $request->ip()
        );

        if (!$result['success']) {
            return redirect()->route('otp.verify')
                ->withErrors(['otp_code' => $result['message']])
                ->with('whatsapp_number', $whatsappNumber);
        }

        // OTP verified - log in the user
        $user = $result['user'];
        
        Auth::login($user, $request->boolean('remember'));
        
        $request->session()->regenerate();
        
        // Clear WhatsApp login session data
        $request->session()->forget('whatsapp_login_number');
        $request->session()->forget('otp_sent');
        $request->session()->forget('whatsapp_number');

        // Redirect based on role
        if ($user->role === 'admin' || $user->role === 'super_admin') {
            return redirect()->intended(route('admin.dashboard'));
        } elseif (in_array($user->role, ['plumber', 'gardener'])) {
            return redirect()->intended(route('service-provider.dashboard'));
        } else {
            return redirect()->intended(route('client.dashboard'));
        }
    }

    /**
     * Resend OTP
     */
    public function resend(Request $request): RedirectResponse
    {
        $whatsappNumber = $request->session()->get('whatsapp_login_number');
        
        if (!$whatsappNumber) {
            return redirect()->route('login')
                ->withErrors(['whatsapp_number' => 'Vraag eerst een OTP-code aan.']);
        }

        $result = $this->otpService->generateAndSendOtp(
            $whatsappNumber,
            $request->ip()
        );

        if ($result['success']) {
            return redirect()->route('otp.verify')
                ->with('success', $result['message'])
                ->with('whatsapp_number', $whatsappNumber);
        } else {
            return redirect()->route('otp.verify')
                ->withErrors(['otp_code' => $result['message']]);
        }
    }
}
