<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ClientRegistrationController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PostcodeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    $categories = \App\Models\Category::where('is_active', true)->get();
    return view('welcome', compact('categories'));
})->name('welcome');

// Role-based dashboard redirect
Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if ($user->role === 'admin' || $user->role === 'super_admin') {
        return redirect()->route('admin.dashboard');
    } elseif (in_array($user->role, ['plumber', 'gardener'])) {
        return redirect()->route('service-provider.dashboard');
    } else {
        return redirect()->route('client.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

// Client Dashboard Routes
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        abort_unless($user && $user->role === 'client', 403);
        
        // Get client's service requests
        $requests = \App\Models\WaRequest::where('customer_id', $user->id)
            ->with('category', 'rating')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get statistics
        $totalRequests = \App\Models\WaRequest::where('customer_id', $user->id)->count();
        $activeRequests = \App\Models\WaRequest::where('customer_id', $user->id)
            ->whereIn('status', ['broadcasting', 'active'])
            ->count();
        $completedRequests = \App\Models\WaRequest::where('customer_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        // Review statistics for client
        $totalReviews = \App\Models\Rating::where('customer_id', $user->id)->count();
        $totalServicesAvailed = $completedRequests;
        
        // Get available service categories
        $categories = \App\Models\Category::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('client.dashboard', compact('requests', 'totalRequests', 'activeRequests', 'completedRequests', 'totalReviews', 'totalServicesAvailed', 'categories'));
    })->name('dashboard');
});

// Registration routes
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// Client Registration routes
Route::get('/client/register', [ClientRegistrationController::class, 'create'])->name('client.register');
Route::post('/client/register', [ClientRegistrationController::class, 'store'])->name('client.register.store');

// Postal Code search + radius
Route::get('/zoek-postcode', [PostcodeController::class, 'zoek'])->name('postcode.search');
Route::get('/werk-radius', [PostcodeController::class, 'radius'])->name('postcode.radius');

// Address autocomplete API
Route::get('/api/address/suggest', [AddressController::class, 'suggest'])->name('address.suggest');

// Checkout routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Service Provider Dashboard Routes
Route::middleware(['auth'])->prefix('service-provider')->name('service-provider.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\ServiceProvider\DashboardController::class, 'index'])->name('dashboard');
    
    // Status update
    Route::post('/status', function (Request $request) {
        $request->validate(['status' => 'required|in:available,busy,holiday']);
        $user = Auth::user();
        if (!in_array($user->role, ['plumber', 'gardener'])) {
            abort(403);
        }
        $user->status = $request->status;
        $user->save();
        return back()->with('success', 'Status updated to ' . ucfirst($request->status));
    })->name('status.update');
    
    // Coverage routes
    Route::get('/coverage', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'index'])->name('coverage.index');
    Route::post('/coverage', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'store'])->name('coverage.store');
    Route::delete('/coverage/{id}', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'destroy'])->name('coverage.destroy');
    Route::post('/coverage/bulk', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'bulkStore'])->name('coverage.bulk');
    Route::post('/coverage/auto-nearby', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'autoAddNearby'])->name('coverage.auto-nearby');
    
    // Municipality search routes
    Route::get('/municipalities/search', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'searchMunicipalities'])->name('municipalities.search');
    Route::get('/municipalities/{name}/towns', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'municipalityTowns'])->name('municipalities.towns');
    Route::get('/municipalities/nearby', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'nearbyMunicipalities'])->name('municipalities.nearby');
    Route::get('/municipalities/distance', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'calculateDistance'])->name('municipalities.distance');
    Route::get('/municipalities/distances', [\App\Http\Controllers\ServiceProvider\CoverageController::class, 'calculateDistances'])->name('municipalities.distances');
    
    // Schedule routes
    Route::get('/schedule', [\App\Http\Controllers\ServiceProvider\ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/schedule', [\App\Http\Controllers\ServiceProvider\ScheduleController::class, 'update'])->name('schedule.update');
    Route::get('/schedule/api', [\App\Http\Controllers\ServiceProvider\ScheduleController::class, 'getSchedule'])->name('schedule.api');
    Route::post('/schedule/availability', [\App\Http\Controllers\ServiceProvider\ScheduleController::class, 'checkAvailability'])->name('schedule.availability');
    
    // Category routes
    Route::get('/categories', [\App\Http\Controllers\ServiceProvider\CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories', [\App\Http\Controllers\ServiceProvider\CategoryController::class, 'update'])->name('categories.update');
});

// Webhook route (no auth required)
Route::post('/checkout/webhook', [CheckoutController::class, 'webhook'])->name('checkout.webhook');

// Admin routes - Super Admin only
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    
    // Category Site Configuration
    Route::get('/categories/{category}/site-config', [\App\Http\Controllers\Admin\CategorySiteConfigController::class, 'show'])->name('categories.site-config.show');
    Route::put('/categories/{category}/site-config', [\App\Http\Controllers\Admin\CategorySiteConfigController::class, 'update'])->name('categories.site-config.update');
    Route::delete('/categories/{category}/site-config/logo', [\App\Http\Controllers\Admin\CategorySiteConfigController::class, 'removeLogo'])->name('categories.site-config.remove-logo');
    Route::delete('/categories/{category}/site-config/favicon', [\App\Http\Controllers\Admin\CategorySiteConfigController::class, 'removeFavicon'])->name('categories.site-config.remove-favicon');
    Route::delete('/categories/{category}/site-config/og-image', [\App\Http\Controllers\Admin\CategorySiteConfigController::class, 'removeOgImage'])->name('categories.site-config.remove-og-image');
    
    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    
    // Subscriptions
    Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('subscriptions');
    
    // WhatsApp Management
    Route::get('/whatsapp', [\App\Http\Controllers\Admin\WhatsAppController::class, 'index'])->name('whatsapp');
    Route::get('/whatsapp/qr', [\App\Http\Controllers\Admin\WhatsAppController::class, 'qr'])->name('whatsapp.qr');
    Route::get('/whatsapp/status', [\App\Http\Controllers\Admin\WhatsAppController::class, 'status'])->name('whatsapp.status');
    Route::post('/whatsapp/logout', [\App\Http\Controllers\Admin\WhatsAppController::class, 'logout'])->name('whatsapp.logout');
    Route::post('/whatsapp/test-send', [\App\Http\Controllers\Admin\WhatsAppController::class, 'testSend'])->name('whatsapp.testSend');
    
    // WhatsApp Flows (will need WaFlowController and models)
    Route::resource('/flows', \App\Http\Controllers\Admin\WaFlowController::class)->names('flows');
    Route::resource('/flows.nodes', \App\Http\Controllers\Admin\WaNodeController::class)->names('flows.nodes');
    
    // Service Providers (Plumbers/Gardeners)
    Route::resource('/service-providers', \App\Http\Controllers\Admin\ServiceProviderController::class)->names('service-providers');
    
    // Clients
    Route::resource('/clients', \App\Http\Controllers\Admin\ClientController::class)->names('clients');
    
    // Requests (will need WaRequestController and models)
    Route::get('/requests', [\App\Http\Controllers\Admin\RequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{request}', [\App\Http\Controllers\Admin\RequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{request}/update-status', [\App\Http\Controllers\Admin\RequestController::class, 'updateStatus'])->name('requests.update-status');
    
    // Tele Records (will need TeleController and models)
    Route::resource('/tele', \App\Http\Controllers\Admin\TeleController::class)->names('tele');
});

// Profile routes (for all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Support page
Route::get('/support', function () {
    return view('support');
})->middleware(['auth'])->name('support');

require __DIR__.'/auth.php';
