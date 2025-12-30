@extends('layouts.modern-dashboard')

@section('title', 'Service Provider Details')

@section('page-title', 'Service Provider Details')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ $serviceProvider->full_name }}</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.service-providers.edit', $serviceProvider) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Bewerken
                        </a>
                        <a href="{{ route('admin.service-providers.index') }}" class="btn btn-outline-secondary">
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
                            <div class="fw-medium">{{ $serviceProvider->full_name }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">E-mail</label>
                            <div>
                                <a href="mailto:{{ $serviceProvider->email }}" class="text-decoration-none">
                                    {{ $serviceProvider->email }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Telefoon</label>
                            <div>
                                @if($serviceProvider->phone)
                                    <a href="tel:{{ preg_replace('/\D+/', '', $serviceProvider->phone) }}" class="text-decoration-none">
                                        {{ $serviceProvider->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">WhatsApp Nummer</label>
                            <div>
                                @if($serviceProvider->whatsapp_number)
                                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $serviceProvider->whatsapp_number) }}" target="_blank" class="text-decoration-none">
                                        <i class="fab fa-whatsapp me-1 text-success"></i>{{ $serviceProvider->whatsapp_number }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Bedrijfsnaam</label>
                            <div>{{ $serviceProvider->company_name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">BTW-nummer</label>
                            <div>{{ $serviceProvider->btw_number ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i>Adres
                            </h5>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Postcode</label>
                            <div>{{ $serviceProvider->postal_code ?? '—' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Stad</label>
                            <div>{{ $serviceProvider->city ?? '—' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted small">Land</label>
                            <div>{{ $serviceProvider->country ?? 'België' }}</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-tags me-2"></i>Service Categorieën
                            </h5>
                        </div>
                        <div class="col-12">
                            @if($serviceProvider->categories->count() > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($serviceProvider->categories as $cat)
                                        <span class="badge bg-primary">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">Geen categorieën geselecteerd</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('admin.service-providers.edit', $serviceProvider) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Bewerk Service Provider
                        </a>
                        <a href="{{ route('admin.service-providers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Terug naar Lijst
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

