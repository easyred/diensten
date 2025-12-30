@extends('layouts.modern-dashboard')

@section('title', 'Tele Record Bewerken')

@section('page-title', 'Tele Record Bewerken')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Bewerk Tele Record: {{ $tele->name }}</h4>
                    <a href="{{ route('admin.tele.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Terug
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tele.update', $tele) }}">
                        @csrf @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Naam <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $tele->name) }}" required
                                       class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="Sent" {{ old('status', $tele->status) === 'Sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="Active" {{ old('status', $tele->status) === 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Called" {{ old('status', $tele->status) === 'Called' ? 'selected' : '' }}>Called</option>
                                    <option value="Interested" {{ old('status', $tele->status) === 'Interested' ? 'selected' : '' }}>Interested</option>
                                    <option value="Paid" {{ old('status', $tele->status) === 'Paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Postcode</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code', $tele->postal_code) }}"
                                       class="form-control @error('postal_code') is-invalid @enderror">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stad</label>
                                <input type="text" name="city" value="{{ old('city', $tele->city) }}"
                                       class="form-control @error('city') is-invalid @enderror">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Datum</label>
                                <input type="date" name="contacted_date" value="{{ old('contacted_date', $tele->contacted_date?->format('Y-m-d')) }}"
                                       class="form-control @error('contacted_date') is-invalid @enderror">
                                @error('contacted_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Bericht</label>
                                <textarea name="message" rows="5" 
                                          class="form-control @error('message') is-invalid @enderror"
                                          placeholder="Voer uw bericht in...">{{ old('message', $tele->message) }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Opslaan
                            </button>
                            <a href="{{ route('admin.tele.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuleren
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

