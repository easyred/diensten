@extends('layouts.modern-dashboard')

@section('title', 'Service Provider Bewerken')

@section('page-title', 'Service Provider Bewerken')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Los de volgende fouten op:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Bewerk Service Provider: {{ $serviceProvider->full_name }}</h4>
                    <a href="{{ route('admin.service-providers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Terug
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.service-providers.update', $serviceProvider) }}">
                        @csrf @method('PUT')

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Accountgegevens
                                </h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Volledige naam <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" value="{{ old('full_name', $serviceProvider->full_name) }}" required
                                       class="form-control @error('full_name') is-invalid @enderror">
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $serviceProvider->email) }}" required
                                       class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefoon</label>
                                <input type="text" name="phone" value="{{ old('phone', $serviceProvider->phone) }}"
                                       class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp nummer</label>
                                <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $serviceProvider->whatsapp_number) }}"
                                       class="form-control @error('whatsapp_number') is-invalid @enderror">
                                @error('whatsapp_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bedrijfsnaam</label>
                                <input type="text" name="company_name" value="{{ old('company_name', $serviceProvider->company_name) }}"
                                       class="form-control @error('company_name') is-invalid @enderror">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">BTW-nummer</label>
                                <input type="text" name="btw_number" value="{{ old('btw_number', $serviceProvider->btw_number) }}"
                                       class="form-control @error('btw_number') is-invalid @enderror">
                                @error('btw_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Adres
                                </h5>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Straatadres</label>
                                <input type="text" name="address" value="{{ old('address', $serviceProvider->address) }}"
                                       class="form-control @error('address') is-invalid @enderror">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Huisnummer</label>
                                <input type="text" name="number" value="{{ old('number', $serviceProvider->number) }}"
                                       class="form-control @error('number') is-invalid @enderror">
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Postcode</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code', $serviceProvider->postal_code) }}"
                                       class="form-control @error('postal_code') is-invalid @enderror">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stad</label>
                                <input type="text" name="city" value="{{ old('city', $serviceProvider->city) }}"
                                       class="form-control @error('city') is-invalid @enderror">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Land</label>
                                <input type="text" name="country" value="{{ old('country', $serviceProvider->country ?? 'België') }}"
                                       class="form-control @error('country') is-invalid @enderror">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-tags me-2"></i>Service Categorieën
                                </h5>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Selecteer categorieën <span class="text-danger">*</span></label>
                                @if($categories && count($categories) > 0)
                                    <div class="border rounded p-3 bg-light">
                                        <div class="row g-2">
                                            @foreach($categories as $cat)
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="form-check p-3 border rounded bg-white">
                                                        <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                                                               class="form-check-input" id="category_{{ $cat->id }}"
                                                               @checked($serviceProvider->categories->contains($cat->id) || in_array($cat->id, old('categories', [])))>
                                                        <label class="form-check-label fw-medium" for="category_{{ $cat->id }}">
                                                            <i class="fas fa-tag me-2 text-primary"></i>
                                                            {{ $cat->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Geen categorieën gevonden.
                                    </div>
                                @endif
                                @error('categories')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Opslaan
                                    </button>
                                    <a href="{{ route('admin.service-providers.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Annuleren
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

