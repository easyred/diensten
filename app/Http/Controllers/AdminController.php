<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\Subscription;

class AdminController extends Controller
{
    public function dashboard()
    {
        // User statistics
        $totalUsers = User::count();
        $totalServiceProviders = User::whereIn('role', ['plumber', 'gardener'])->count();
        $totalPlumbers = User::where('role', 'plumber')->count();
        $totalGardeners = User::where('role', 'gardener')->count();
        $totalClients = User::where('role', 'client')->count();
        
        // Subscription statistics
        $activeSubscriptions = Subscription::where('status', 'active')->where('ends_at', '>', now())->count();
        $totalSubscriptions = Subscription::count();
        
        // Domain statistics
        $totalDomains = Category::where('is_active', true)->count();
        
        // Recent activity (placeholder - you can implement actual activity tracking)
        $recentActivity = collect([
            (object) [
                'description' => 'Nieuwe service provider geregistreerd',
                'created_at' => now()->subHours(2)
            ],
            (object) [
                'description' => 'Nieuw abonnement geactiveerd',
                'created_at' => now()->subHours(4)
            ],
            (object) [
                'description' => 'Nieuw domain toegevoegd',
                'created_at' => now()->subHours(6)
            ]
        ]);

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalServiceProviders',
            'totalPlumbers',
            'totalGardeners',
            'totalClients',
            'activeSubscriptions',
            'totalSubscriptions',
            'totalDomains',
            'recentActivity'
        ));
    }

    public function categories()
    {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:categories',
            'name' => 'required',
        ]);

        Category::create([
            'code' => $request->code,
            'name' => $request->name,
            'domain' => $request->domain,
            'logo_url' => $request->logo_url,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('admin.categories')->with('success', 'Domain created successfully');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'code' => 'required|unique:categories,code,' . $category->id,
            'name' => 'required',
        ]);

        $category->update([
            'code' => $request->code,
            'name' => $request->name,
            'domain' => $request->domain,
            'logo_url' => $request->logo_url,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('admin.categories')->with('success', 'Domain updated successfully');
    }

    public function users()
    {
        $users = User::with('categories')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::with('user')->paginate(20);
        return view('admin.subscriptions', compact('subscriptions'));
    }
}
