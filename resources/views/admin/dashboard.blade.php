@extends('layouts.modern-dashboard')

@section('title', 'Admin Dashboard')

@section('page-title', 'Admin Dashboard')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <!-- Welkomstsectie -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-2">Welkom terug, {{ Auth::user()->full_name ?? Auth::user()->email }}!</h4>
                            <p class="text-muted mb-0">Beheer en monitor het diensten.pro platform vanuit uw admin dashboard.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="user-avatar" style="width: 64px; height: 64px; font-size: 1.5rem; margin: 0 auto;">
                                {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->email, 0, 1)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistieken Kaarten -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="stats-number">{{ $totalUsers ?? 0 }}</div>
                        <div class="stats-label">Totaal Gebruikers</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card success">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <div class="stats-number">{{ $totalServiceProviders ?? 0 }}</div>
                        <div class="stats-label">Service Providers</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card info">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="stats-number">{{ $totalClients ?? 0 }}</div>
                        <div class="stats-label">Cliënten</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card warning">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div>
                        <div class="stats-number">{{ $activeSubscriptions ?? 0 }}</div>
                        <div class="stats-label">Actieve Abonnementen</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Platform Overzicht -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Platform Overzicht
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="stats-icon me-3" style="width: 40px; height: 40px; font-size: 1rem; background: var(--primary-color);">
                                    <i class="fas fa-tags"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Actieve Domains</h6>
                                    <p class="text-muted mb-0">{{ $totalDomains ?? 0 }} totaal</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="stats-icon me-3" style="width: 40px; height: 40px; font-size: 1rem; background: var(--success-color);">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Loodgieters</h6>
                                    <p class="text-muted mb-0">{{ $totalPlumbers ?? 0 }} totaal</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="stats-icon me-3" style="width: 40px; height: 40px; font-size: 1rem; background: var(--info-color);">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Tuinmannen</h6>
                                    <p class="text-muted mb-0">{{ $totalGardeners ?? 0 }} totaal</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="stats-icon me-3" style="width: 40px; height: 40px; font-size: 1rem; background: var(--warning-color);">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Totaal Abonnementen</h6>
                                    <p class="text-muted mb-0">{{ $totalSubscriptions ?? 0 }} totaal</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Recente Activiteit
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if(isset($recentActivity) && count($recentActivity) > 0)
                            @foreach($recentActivity->take(5) as $activity)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-icon me-3">
                                            <i class="fas fa-circle text-primary" style="font-size: 0.5rem;"></i>
                                        </div>
                                        <div>
                                            <p class="mb-1 small">{{ $activity->description ?? 'Activiteit' }}</p>
                                            <small class="text-muted">{{ $activity->created_at?->diffForHumans() ?? 'Onlangs' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                <p class="text-muted small">Geen recente activiteit</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Snelle Acties -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Snelle Acties
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.categories') }}" class="btn btn-outline-primary">
                            <i class="fas fa-tags me-2"></i>
                            Beheer Domains
                        </a>
                        <a href="{{ route('admin.whatsapp') }}" class="btn btn-outline-success">
                            <i class="fab fa-whatsapp me-2"></i>
                            WhatsApp Beheer
                        </a>
                        <a href="{{ route('admin.flows.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-project-diagram me-2"></i>
                            Beheer Stroomlijnen
                        </a>
                        <a href="{{ route('admin.service-providers.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-user-tie me-2"></i>
                            Beheer Service Providers
                        </a>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-warning">
                            <i class="fas fa-users me-2"></i>
                            Beheer Cliënten
                        </a>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user-friends me-2"></i>
                            Alle Gebruikers
                        </a>
                        <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline-success">
                            <i class="fas fa-credit-card me-2"></i>
                            Abonnementen
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Systeem Informatie
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p class="text-muted mb-1">Platform Versie</p>
                            <p class="fw-semibold">v1.0.0</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1">Laatst Bijgewerkt</p>
                            <p class="fw-semibold">{{ now()->format('M d, Y') }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1">Database Status</p>
                            <p class="fw-semibold text-success">Verbonden</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1">WhatsApp Bot</p>
                            <p class="fw-semibold text-success">Actief</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1">Betalingsgateway</p>
                            <p class="fw-semibold text-success">Mollie</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1">Omgeving</p>
                            <p class="fw-semibold">{{ config('app.env') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
    }
    
    .timeline-item {
        position: relative;
    }
    
    .timeline-icon {
        position: relative;
        top: 0.25rem;
    }
</style>
@endpush
