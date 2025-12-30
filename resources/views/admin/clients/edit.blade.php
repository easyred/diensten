@extends('layouts.modern-dashboard')

@section('title', 'Klant Bewerken')

@section('page-title', 'Klant Bewerken')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
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
                    <h4 class="card-title mb-0">Bewerk Klant: {{ $client->full_name }}</h4>
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Terug
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.clients.update', $client) }}">
                        @csrf @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Volledige naam <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" value="{{ old('full_name', $client->full_name) }}" required
                                       class="form-control @error('full_name') is-invalid @enderror">
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefoon</label>
                                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                                       class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">WhatsApp nummer</label>
                                <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $client->whatsapp_number) }}"
                                       class="form-control @error('whatsapp_number') is-invalid @enderror">
                                @error('whatsapp_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Postcode</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code', $client->postal_code) }}"
                                       class="form-control @error('postal_code') is-invalid @enderror">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Stad</label>
                                <input type="text" name="city" value="{{ old('city', $client->city) }}"
                                       class="form-control @error('city') is-invalid @enderror">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Opslaan
                            </button>
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuleren
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

