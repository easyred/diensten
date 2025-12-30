@extends('layouts.modern-dashboard')

@section('title', 'WhatsApp Beheer')

@section('page-title', 'WhatsApp Beheer')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <!-- Left: Status/QR of Verbonden -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fab fa-whatsapp me-2"></i>
                    Verbinding Status
                </h5>
                @php
                    $isConnected = isset($status['status']) && in_array(strtolower($status['status'] ?? ''), ['connected', 'connected']);
                @endphp
                
                @if($isConnected)
                    <button type="button" class="btn btn-outline-danger btn-sm" 
                            data-bs-toggle="modal" data-bs-target="#disconnectModal">
                        <i class="fas fa-unlink me-1"></i>
                        Ontkoppelen
                    </button>
                @else
                    <button type="button" class="btn btn-outline-success btn-sm" 
                            onclick="location.reload()">
                        <i class="fas fa-sync me-1"></i>
                        Vernieuwen
                    </button>
                @endif
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted">Status:</span>
                    <span class="badge bg-info ms-2">
                        {{ is_array($status) ? ($status['status'] ?? 'Unknown') : ($status ?? 'Unknown') }}
                    </span>
                </div>
                

                @if($error)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ $error }}
                    </div>
                @endif

                @if ($qr && !$isConnected)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Scan deze QR-code met uw WhatsApp:</strong><br>
                        WhatsApp → Instellingen → Koppel een apparaat
                    </div>
                    <div class="text-center p-3 border border-dashed rounded bg-light">
                        <img src="{{ $qr }}" alt="WhatsApp QR Code" class="img-fluid" style="max-height: 280px; max-width: 100%;">
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">De QR-code wordt automatisch vernieuwd</small>
                    </div>
                @elseif($isConnected)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>WhatsApp is verbonden!</strong><br>
                        U kunt nu berichten verzenden via het platform.
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>QR-code wordt geladen...</strong><br>
                        Als de QR-code niet verschijnt, controleer of de WhatsApp bot actief is op: <code>{{ $base ?? 'http://127.0.0.1:3000' }}</code>
                    </div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" onclick="location.reload()">
                            <i class="fas fa-sync me-2"></i>Vernieuw Pagina
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right: Testformulier -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-paper-plane me-2"></i>
                    Verstuur Testbericht
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.whatsapp.testSend') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="number" class="form-label">WhatsApp Nummer</label>
                        <input type="text" id="number" name="number" 
                               class="form-control" 
                               placeholder="bijv. 32470123456" required>
                        <div class="form-text">Voer het nummer in zonder speciale tekens (bijv. 32470123456)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Bericht</label>
                        <textarea id="message" name="message" 
                                  class="form-control" 
                                  rows="4" 
                                  placeholder="Typ je testbericht" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>
                        Verstuur Testbericht
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Aanvullende Informatie -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    WhatsApp Bot Informatie
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Verbindingsdetails</h6>
                        <ul class="list-unstyled">
                            <li><strong>Bot Status:</strong> 
                                <span class="badge bg-info">
                                    {{ is_array($status) ? ($status['status'] ?? 'Onbekend') : ($status ?? 'Onbekend') }}
                                </span>
                            </li>
                            <li><strong>Bot URL:</strong> {{ $base ?? 'http://127.0.0.1:3000' }}</li>
                            <li><strong>Laatst bijgewerkt:</strong> {{ now()->format('M d, Y H:i:s') }}</li>
                            <li><strong>Omgeving:</strong> {{ config('app.env') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Snel Acties</h6>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100" 
                                    data-bs-toggle="modal" data-bs-target="#disconnectModal">
                                <i class="fas fa-unlink me-2"></i>
                                Ontkoppel WhatsApp
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Terug naar Dashboard
                            </a>
                            <a href="{{ route('admin.flows.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-project-diagram me-2"></i>
                                Beheer Stroomlijnen
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ontkoppel WhatsApp Modal -->
<div class="modal fade" id="disconnectModal" tabindex="-1" aria-labelledby="disconnectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="disconnectModalLabel">
                    <i class="fas fa-unlink text-danger me-2"></i>
                    Ontkoppel WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="text-center mb-4">
                    <div class="mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #ff6b6b, #ee5a52); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-unlink text-white" style="font-size: 2rem;"></i>
                    </div>
                    <h6 class="text-dark mb-2">Ontkoppelen en opnieuw verbinden?</h6>
                    <p class="text-muted mb-0">
                        Dit zal alle sessiegegevens wissen en een nieuwe QR-code tonen voor een nieuwe verbinding.
                    </p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Annuleren
                </button>
                <form method="POST" action="{{ route('admin.whatsapp.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-unlink me-2"></i>
                        Ontkoppelen & Toon QR
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Automatisch de pagina vernieuwen na succesvolle ontkoppeling om een nieuwe QR-code te tonen
@if(session('success') && (str_contains(session('success'), 'disconnected') || str_contains(session('success'), 'cleared')))
    setTimeout(function() {
        location.reload();
    }, 2000); // Vernieuw na 2 seconden
@endif

// Auto-refresh QR code every 30 seconds if not connected
@if(!$isConnected)
    setInterval(function() {
        fetch('{{ route("admin.whatsapp.qr") }}')
            .then(response => response.json())
            .then(data => {
                if (data.qr) {
                    const img = document.querySelector('img[alt="WhatsApp QR Code"]');
                    if (img) {
                        img.src = data.qr; // Bot returns full data URL
                    }
                }
            })
            .catch(err => console.log('QR refresh error:', err));
    }, 30000); // Refresh every 30 seconds
@endif
</script>
@endsection

