@extends('layouts.modern-dashboard')

@section('title', 'Klant Details')

@section('page-title', 'Klant Details')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $client->full_name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Bewerken
                        </a>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Terug
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Account Gegevens
                            </h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Volledige Naam</label>
                            <div class="fw-medium">{{ $client->full_name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">E-mail</label>
                            <div>
                                <a href="mailto:{{ $client->email }}" class="text-decoration-none">
                                    {{ $client->email }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Telefoon</label>
                            <div>
                                @if($client->phone)
                                    <a href="tel:{{ preg_replace('/\D+/', '', $client->phone) }}" class="text-decoration-none">
                                        {{ $client->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">WhatsApp</label>
                            <div>
                                @if($client->whatsapp_number)
                                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $client->whatsapp_number) }}" target="_blank" class="text-decoration-none text-success">
                                        <i class="fab fa-whatsapp me-1"></i>{{ $client->whatsapp_number }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Bedrijfsnaam</label>
                            <div>{{ $client->company_name ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Locatie
                            </h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Postcode</label>
                            <div>{{ $client->postal_code ?? '—' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Stad</label>
                            <div>{{ $client->city ?? '—' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Land</label>
                            <div>{{ $client->country ?? 'België' }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Bewerk Klant
                        </a>
                        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Terug naar Lijst
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

