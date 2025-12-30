<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function checkout()
    {
        return view('checkout');
    }

    public function success()
    {
        return view('checkout.success');
    }

    public function webhook(Request $request)
    {
        // Handle Mollie webhook
        return response()->json(['status' => 'ok']);
    }
}
