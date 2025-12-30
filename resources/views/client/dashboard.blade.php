 
@extends('layouts.dashboard-top-header')

@section('title', 'Cliënt Dashboard')

@push('styles')
<style>
    /* Stats Cards */
    .stats-card {
        background: var(--card-bg-light);
        border-radius: 16px;
        border: 1px solid var(--border-light);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--green-accent), rgba(16, 185, 129, 0.5));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .stats-card:hover {
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08);
        transform: translateY(-3px);
        border-color: rgba(16, 185, 129, 0.2);
    }

    .stats-card:hover::before {
        opacity: 1;
    }

    body.dark .stats-card {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    body.dark .stats-card:hover {
        box-shadow: 0 8px 28px rgba(16, 185, 129, 0.15), 0 2px 8px rgba(0, 0, 0, 0.4);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .stats-card .stats-icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-right: 1.25rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        flex-shrink: 0;
        position: relative;
    }

    .stats-card:hover .stats-icon-circle {
        transform: scale(1.08) translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .stats-card .flex-grow-1 {
        flex: 1;
        min-width: 0;
    }

    .stats-icon-blue {
        background-color: #3b82f6;
    }

    .stats-icon-yellow {
        background-color: #f59e0b;
    }

    .stats-icon-green {
        background-color: var(--green-accent);
    }

    .stats-icon-purple {
        background-color: #8b5cf6;
    }

    .stats-label {
        color: var(--text-secondary-light);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        text-transform: none;
        letter-spacing: 0.3px;
    }

    body.dark .stats-label {
        color: var(--text-secondary-dark);
    }

    .stats-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--text-primary-light);
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    body.dark .stats-number {
        color: var(--text-primary-dark);
    }

    /* Buttons */
    .btn-primary-custom {
        background-color: var(--green-accent);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
    }

    .btn-primary-custom:hover {
        background-color: var(--green-hover);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        transform: translateY(-1px);
    }

    body.dark .fw-semibold {
        color: var(--text-primary-dark) !important;
    }

    /* Professional Quick Actions Buttons */
    .quick-action-btn {
        background: var(--card-bg-light);
        border: 1px solid var(--border-light);
        color: var(--text-primary-light);
        padding: 0.875rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.75rem;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .quick-action-btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        color: var(--text-primary-light);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        transform: none;
    }

    .quick-action-btn i {
        color: #6b7280;
        font-size: 0.875rem;
        width: 18px;
        text-align: center;
    }

    .quick-action-btn:hover i {
        color: var(--text-primary-light);
    }

    body.dark .quick-action-btn {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        color: var(--text-primary-dark);
    }

    body.dark .quick-action-btn:hover {
        background: #3a3f47;
        border-color: #4b5563;
        color: var(--text-primary-dark);
    }

    body.dark .quick-action-btn i {
        color: #9ca3af;
    }

    body.dark .quick-action-btn:hover i {
        color: var(--text-primary-dark);
    }

    /* Account Information Styling */
    .account-info-label {
        color: #6b7280;
        font-size: 0.8125rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .account-info-value {
        color: var(--text-primary-light);
        font-size: 0.9375rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
    }

    body.dark .account-info-label {
        color: #9ca3af;
    }

    body.dark .account-info-value {
        color: var(--text-primary-dark);
    }

    /* Remove colored icons from card titles */
    .dashboard-card-title i {
        color: #6b7280 !important;
    }

    body.dark .dashboard-card-title i {
        color: #9ca3af !important;
    }

    /* Service Cards */
    .service-card:hover {
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        transform: translateY(-2px);
        border-color: var(--green-accent);
    }

    body.dark .service-card {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
    }

    body.dark .service-card:hover {
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        border-color: var(--green-accent);
    }

    /* Service Statistics Cards */
    body.dark .dashboard-card-body .text-center {
        background: var(--card-bg-dark) !important;
        border-color: var(--border-dark) !important;
    }

    body.dark .dashboard-card-body .text-center h3 {
        color: var(--text-primary-dark) !important;
    }

    body.dark .dashboard-card-body .text-center p {
        color: var(--text-secondary-dark) !important;
    }
</style>
@endpush

@section('content')
<!-- Stats Cards Row -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon-circle stats-icon-blue">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-label">Totaal Verzoeken</div>
                    <div class="stats-number">{{ $totalRequests ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon-circle stats-icon-yellow">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-label">Actieve Verzoeken</div>
                    <div class="stats-number">{{ $activeRequests ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon-circle stats-icon-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-label">Voltooide Klussen</div>
                    <div class="stats-number">{{ $completedRequests ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon-circle stats-icon-purple">
                    <i class="fas fa-star"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-label">Gegeven Beoordelingen</div>
                    <div class="stats-number">{{ $totalReviews ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Statistics Section -->
@if(isset($totalServicesAvailed) && $totalServicesAvailed > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-chart-line me-2"></i>
                    <span>Service Statistieken</span>
                </h5>
            </div>
            <div class="dashboard-card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 style="color: var(--green-accent); font-weight: 700; margin-bottom: 0.5rem;">
                                {{ $totalServicesAvailed }}
                            </h3>
                            <p class="text-muted mb-0">Totaal Services Gebruikt</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 style="color: var(--green-accent); font-weight: 700; margin-bottom: 0.5rem;">
                                {{ $totalReviews ?? 0 }}
                            </h3>
                            <p class="text-muted mb-0">Totaal Beoordelingen Gegeven</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <h3 style="color: var(--green-accent); font-weight: 700; margin-bottom: 0.5rem;">
                                @php
                                    $avgRating = $requests->where('rating')->avg('rating');
                                @endphp
                                @if($avgRating)
                                    {{ number_format($avgRating, 1) }}/5
                                @else
                                    N/A
                                @endif
                            </h3>
                            <p class="text-muted mb-0">Gemiddelde Beoordeling</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Requests -->
@if(isset($requests) && $requests->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-list"></i>
                    <span>Recente Verzoeken</span>
                </h5>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Datum</th>
                                <th>Categorie</th>
                                <th>Probleem</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                            <tr>
                                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $request->category->name ?? 'N/A' }}</td>
                                <td>{{ $request->problem_type ?? $request->problem }}</td>
                                <td>
                                    <span class="badge bg-{{ $request->status === 'completed' ? 'success' : ($request->status === 'active' ? 'primary' : 'warning') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Subscription Status -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-credit-card me-2"></i>
                    Abonnement Status
                </h5>
            </div>
            <div class="dashboard-card-body">
                @php
                    $user = Auth::user();
                    $subscription = $user->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->latest()->first();
                @endphp
                @if($subscription)
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 style="color: var(--green-accent); margin-bottom: 0.5rem;">
                                <i class="fas fa-check-circle me-2"></i>
                                Actief Abonnement
                            </h6>
                            <p class="text-muted mb-0">
                                Plan: <strong>{{ $subscription->plan_name ?? 'Premium' }}</strong>
                            </p>
                            @if($subscription->ends_at)
                                <p class="text-muted mb-0">
                                    Verloopt: <strong>{{ $subscription->ends_at->format('M d, Y') }}</strong>
                                </p>
                            @endif
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span style="background-color: var(--green-accent); color: white; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 500;">Actief</span>
                        </div>
                    </div>
                @else
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 style="color: #f59e0b; margin-bottom: 0.5rem;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Geen Actief Abonnement
                            </h6>
                            <p class="text-muted mb-0">
                                Abonneer op een plan om toegang te krijgen tot services.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('welcome') }}#pricing" class="btn-primary-custom">
                                <i class="fas fa-credit-card"></i>
                                Abonneer Nu
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- My Services Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-concierge-bell me-2"></i>
                    <span>Mijn Services</span>
                </h5>
            </div>
            <div class="dashboard-card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3" style="
                            background: var(--card-bg-light);
                            border: 1px solid var(--border-light);
                            border-radius: 12px;
                            height: 100%;
                        ">
                            <div class="mb-2">
                                <i class="fas fa-list-alt" style="font-size: 2rem; color: var(--green-accent);"></i>
                            </div>
                            <h3 style="color: var(--text-primary-light); font-weight: 700; margin-bottom: 0.5rem;">
                                {{ $totalRequests ?? 0 }}
                            </h3>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">Totaal Services</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3" style="
                            background: var(--card-bg-light);
                            border: 1px solid var(--border-light);
                            border-radius: 12px;
                            height: 100%;
                        ">
                            <div class="mb-2">
                                <i class="fas fa-clock" style="font-size: 2rem; color: #f59e0b;"></i>
                            </div>
                            <h3 style="color: var(--text-primary-light); font-weight: 700; margin-bottom: 0.5rem;">
                                {{ $activeRequests ?? 0 }}
                            </h3>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">Actieve Services</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3" style="
                            background: var(--card-bg-light);
                            border: 1px solid var(--border-light);
                            border-radius: 12px;
                            height: 100%;
                        ">
                            <div class="mb-2">
                                <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--green-accent);"></i>
                            </div>
                            <h3 style="color: var(--text-primary-light); font-weight: 700; margin-bottom: 0.5rem;">
                                {{ $completedRequests ?? 0 }}
                            </h3>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">Voltooide Services</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="text-center p-3" style="
                            background: var(--card-bg-light);
                            border: 1px solid var(--border-light);
                            border-radius: 12px;
                            height: 100%;
                        ">
                            <div class="mb-2">
                                <i class="fas fa-star" style="font-size: 2rem; color: #8b5cf6;"></i>
                            </div>
                            <h3 style="color: var(--text-primary-light); font-weight: 700; margin-bottom: 0.5rem;">
                                {{ $totalReviews ?? 0 }}
                            </h3>
                            <p class="text-muted mb-0" style="font-size: 0.875rem;">Beoordelingen Gegeven</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-bolt"></i>
                    <span>Snel Acties</span>
                </h5>
            </div>
            <div class="dashboard-card-body">
                <div class="d-grid gap-2">
                    <a href="https://wa.me/32490458009?text={{ urlencode('Hallo, ik wil een service aanvragen.') }}" 
                       target="_blank" 
                       class="quick-action-btn">
                        <i class="fab fa-whatsapp"></i>
                        <span>Nieuwe Service Aanvragen</span>
                    </a>
                    <a href="{{ route('welcome') }}#pricing" class="quick-action-btn">
                        <i class="fas fa-credit-card"></i>
                        <span>Beheer Abonnement</span>
                    </a>
                    <a href="{{ route('support') }}" class="quick-action-btn">
                        <i class="fas fa-headset"></i>
                        <span>Neem Contact Op met Ondersteuning</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="quick-action-btn">
                        <i class="fas fa-user-cog"></i>
                        <span>Profiel Bijwerken</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Accountinformatie</span>
                </h5>
            </div>
            <div class="dashboard-card-body">
                <div class="row">
                    <div class="col-6">
                        <p class="account-info-label">Naam</p>
                        <p class="account-info-value">{{ Auth::user()->full_name ?? 'Niet ingesteld' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="account-info-label">E-mail</p>
                        <p class="account-info-value">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="col-6">
                        <p class="account-info-label">Stad</p>
                        <p class="account-info-value">{{ Auth::user()->city ?? 'Niet ingesteld' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="account-info-label">WhatsApp</p>
                        <p class="account-info-value">{{ Auth::user()->whatsapp_number ? format_phone_number(Auth::user()->whatsapp_number) : 'Niet ingesteld' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

