<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class WhatsAppLoginController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the WhatsApp login form
     */
    public function show(Request $request): View
    {
        // Clear any existing OTP session when user wants to use another number
        $request->session()->forget('whatsapp_login_number');
        $request->session()->forget('otp_sent');
        $request->session()->forget('whatsapp_number');
        
        return view('auth.whatsapp-login');
    }

    /**
     * Handle WhatsApp login request - send OTP
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'whatsapp_number' => ['required', 'string', 'regex:/^[0-9+\-\s()]+$/'],
        ], [
            'whatsapp_number.required' => 'WhatsApp-nummer is verplicht.',
            'whatsapp_number.regex' => 'Voer een geldig WhatsApp-nummer in.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $whatsappNumber = preg_replace('/[^0-9]/', '', $request->whatsapp_number);
        
        // Basic validation - should be at least 8 digits
        if (strlen($whatsappNumber) < 8) {
            return back()->withErrors(['whatsapp_number' => 'Voer een geldig WhatsApp-nummer in.'])->withInput();
        }

        // Generate and send OTP
        $result = $this->otpService->generateAndSendOtp(
            $whatsappNumber,
            $request->ip()
        );

        if ($result['success']) {
            // Store WhatsApp number in session for verification step
            $request->session()->put('whatsapp_login_number', $whatsappNumber);
            $request->session()->put('otp_sent', true);
            
            // Redirect to OTP verification page
            return redirect()->route('otp.verify')
                ->with('success', $result['message'])
                ->with('whatsapp_number', $whatsappNumber);
        } else {
            return back()->withErrors(['whatsapp_number' => $result['message']])->withInput();
        }
    }
}
