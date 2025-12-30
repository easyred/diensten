 
@php
    $userRole = Auth::user()->role ?? null;

    $layout = ($userRole === 'admin' || $userRole === 'super_admin')
        ? 'layouts.modern-dashboard'
        : 'layouts.dashboard-top-header';
@endphp

@extends($layout)

{{-- Sidebar (Admin Only) --}}
@section('sidebar-nav')
    @if($userRole === 'admin' || $userRole === 'super_admin')
        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.whatsapp') }}" class="nav-link">
                <i class="fab fa-whatsapp"></i><span>WhatsApp</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.flows.index') }}" class="nav-link">
                <i class="fas fa-project-diagram"></i><span>WhatsApp Stroom</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.service-providers.index') }}" class="nav-link">
                <i class="fas fa-user-tie"></i><span>Service Providers</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.clients.index') }}" class="nav-link">
                <i class="fas fa-users"></i><span>Klanten</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('admin.requests.index') }}" class="nav-link">
                <i class="fas fa-tools"></i><span>Serviceverzoeken</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('support') }}" class="nav-link active">
                <i class="fas fa-headset"></i><span>Ondersteuning</span>
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('profile.edit') }}" class="nav-link">
                <i class="fas fa-user"></i><span>Profiel</span>
            </a>
        </div>
    @endif
@endsection

@section('title', 'Ondersteuning')
@section('page-title', 'Ondersteuning')

@section('content')

<style>
    /* Smooth card UI */
    .support-card {
        border-radius: 12px;
        transition: 0.2s all ease-in-out;
        border: none !important;
        box-shadow: 0px 4px 15px rgba(0,0,0,0.06);
    }

    .support-card:hover {
        transform: translateY(-3px);
        box-shadow: 0px 6px 20px rgba(0,0,0,0.10);
    }

    .section-title {
        font-weight: 600;
        font-size: 18px;
        color: #2E3A59;
    }

    .icon-badge {
        width: 42px;
        height: 42px;
        background: #f1f4ff;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 20px;
    }

    .list-check i {
        font-size: 14px;
        padding-right: 8px;
    }
</style>

<div class="row">
    <div class="col-12">
        
        <div class="card support-card">

            <div class="card-header bg-light py-3">
                <h4 class="mb-0">
                    <i class="fas fa-headset me-2 text-primary"></i>
                    Hoe kunnen we je helpen?
                </h4>
            </div>

            <div class="card-body">

                <p class="text-muted mb-4">
                    We zijn hier om je te helpen. Kies een ondersteuningsoptie hieronder of krijg onmiddellijk hulp.
                </p>

                {{-- Support Options --}}
                <div class="row gy-4">

                    <div class="col-md-6">
                        <div class="card support-card border-start border-4 border-primary">
                            <div class="card-body">

                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-badge">
                                        <i class="fas fa-envelope text-primary"></i>
                                    </div>
                                    <h5 class="ms-3 mb-0 section-title">E-mailondersteuning</h5>
                                </div>

                                <p class="text-muted">Ontvang gedetailleerde hulp via e-mail.</p>

                                <a href="mailto:support@diensten.pro" class="btn btn-primary">
                                    <i class="fas fa-envelope me-2"></i> support@diensten.pro
                                </a>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card support-card border-start border-4 border-success">
                            <div class="card-body">

                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-badge">
                                        <i class="fab fa-whatsapp text-success"></i>
                                    </div>
                                    <h5 class="ms-3 mb-0 section-title">WhatsApp-ondersteuning</h5>
                                </div>

                                <p class="text-muted">Chat onmiddellijk met ons ondersteuningsteam.</p>

                                <a href="https://wa.me/32123456789" target="_blank" class="btn btn-success">
                                    <i class="fab fa-whatsapp me-2"></i> +32 490 46 80 09
                                </a>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hours & Response Time --}}
                <div class="row gy-4 mt-1">

                    <div class="col-md-6">
                        <div class="card support-card border-start border-4 border-info">
                            <div class="card-body">
                                
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-badge">
                                        <i class="fas fa-clock text-info"></i>
                                    </div>
                                    <h5 class="ms-3 mb-0 section-title">Ondersteuningsuren</h5>
                                </div>

                                <ul class="list-unstyled text-muted mb-0 list-check">
                                    <li><i class="fas fa-check text-info"></i> Ma–Vr: 09:00 – 18:00</li>
                                    <li><i class="fas fa-check text-info"></i> Zaterdag: 10:00 – 16:00</li>
                                    <li><i class="fas fa-check text-info"></i> Zondag: Gesloten</li>
                                </ul>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card support-card border-start border-4 border-warning">
                            <div class="card-body">

                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-badge">
                                        <i class="fas fa-bolt text-warning"></i>
                                    </div>
                                    <h5 class="ms-3 mb-0 section-title">Respons Tijd</h5>
                                </div>

                                <ul class="list-unstyled text-muted mb-0 list-check">
                                    <li><i class="fas fa-check text-warning"></i> E-mail: 24 uur</li>
                                    <li><i class="fas fa-check text-warning"></i> WhatsApp: 2 uur</li>
                                    <li><i class="fas fa-check text-warning"></i> Dringend: Onmiddellijk</li>
                                </ul>

                            </div>
                        </div>
                    </div>

                </div>

                {{-- Common Issues --}}
                <div class="card support-card border-start border-4 border-danger mt-2">
                    <div class="card-body">

                        <h5 class="section-title mb-3">
                            <i class="fas fa-exclamation-circle text-danger me-2"></i>
                            Veelvoorkomende Problemen & Snelle Oplossingen
                        </h5>

                        <div class="row">

                            <div class="col-md-6">
                                <ul class="list-unstyled list-check text-muted">
                                    <li><i class="fas fa-check text-success"></i> Kun je geen berichten verzenden? Herstart WhatsApp.</li>
                                    <li><i class="fas fa-check text-success"></i> Geen antwoorden? Controleer je internet.</li>
                                </ul>
                            </div>

                            <div class="col-md-6">
                                <ul class="list-unstyled list-check text-muted">
                                    <li><i class="fas fa-check text-success"></i> Verkeerd adres? Werk je profiel bij.</li>
                                    <li><i class="fas fa-check text-success"></i> Betalingsprobleem? Neem contact op met de facturering.</li>
                                </ul>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary px-4 py-2">
                        <i class="fas fa-arrow-left me-2"></i>
                        Terug naar Dashboard
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

