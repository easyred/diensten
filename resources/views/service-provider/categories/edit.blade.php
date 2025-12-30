 
@extends('layouts.dashboard-top-header')

@section('title', 'Servicecategorieën')

@section('page-title', 'Servicecategorieën')

@section('sidebar-nav')
    <x-service-provider-sidebar />
@endsection

@push('styles')
<style>
    /* Categories Page Styling - Matching Other Plumber Pages */
    .categories-card {
        background: var(--card-bg-light);
        border-radius: 16px;
        border: 1px solid var(--border-light);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
    }

    body.dark .categories-card {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .categories-card-header {
        padding: 1.75rem 2rem;
        border-bottom: 1px solid var(--border-light);
        background: transparent;
    }

    body.dark .categories-card-header {
        border-bottom-color: var(--border-dark);
    }

    .categories-card-title {
        font-size: 1.375rem;
        font-weight: 700;
        color: var(--text-primary-light);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .categories-card-title i {
        color: #6b7280;
        font-size: 1.25rem;
    }

    body.dark .categories-card-title {
        color: var(--text-primary-dark);
    }

    body.dark .categories-card-title i {
        color: #9ca3af;
    }

    .categories-card-body {
        padding: 2rem;
    }

    .categories-subtitle {
        color: var(--text-secondary-light);
        font-size: 0.9375rem;
        margin: 0.75rem 0 0 0;
    }

    body.dark .categories-subtitle {
        color: var(--text-secondary-dark);
    }

    /* Group Card */
    .group-card {
        background: var(--card-bg-light);
        border: 1px solid var(--border-light);
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.5rem;
        transition: all 0.2s ease;
    }

    .group-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    body.dark .group-card {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    body.dark .group-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    .group-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-light);
        background: transparent;
    }

    body.dark .group-card-header {
        border-bottom-color: var(--border-dark);
    }

    .group-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary-light);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .group-card-title i {
        color: var(--green-accent);
        font-size: 0.9375rem;
    }

    body.dark .group-card-title {
        color: var(--text-primary-dark);
    }

    .group-card-body {
        padding: 1.5rem;
    }

    /* Form Check Styling */
    .form-check {
        padding: 0.75rem;
        border-radius: 8px;
        transition: background-color 0.2s ease;
        margin-bottom: 0.5rem;
    }

    .form-check:hover {
        background-color: rgba(16, 185, 129, 0.05);
    }

    body.dark .form-check:hover {
        background-color: rgba(16, 185, 129, 0.1);
    }

    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        margin-top: 0.125rem;
        border: 2px solid var(--border-light);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .form-check-input:checked {
        background-color: var(--green-accent);
        border-color: var(--green-accent);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .form-check-input:focus {
        border-color: var(--green-accent);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    body.dark .form-check-input {
        background-color: var(--card-bg-dark);
        border-color: var(--border-dark);
    }

    body.dark .form-check-input:checked {
        background-color: var(--green-accent);
        border-color: var(--green-accent);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    .form-check-label {
        color: var(--text-primary-light);
        font-size: 0.9375rem;
        font-weight: 500;
        cursor: pointer;
        margin-left: 0.5rem;
        user-select: none;
    }

    body.dark .form-check-label {
        color: var(--text-primary-dark);
    }

    /* Buttons */
    .btn-categories {
        background: linear-gradient(135deg, var(--green-accent) 0%, #059669 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-categories:hover {
        background: linear-gradient(135deg, #059669 0%, var(--green-accent) 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        color: white;
    }

    .btn-categories-outline {
        background: var(--card-bg-light);
        color: var(--text-primary-light);
        border: 1px solid var(--border-light);
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-categories-outline:hover {
        background: #f9fafb;
        border-color: var(--green-accent);
        color: var(--green-accent);
    }

    body.dark .btn-categories-outline {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        color: var(--text-primary-dark);
    }

    body.dark .btn-categories-outline:hover {
        background: #374151;
        border-color: var(--green-accent);
        color: var(--green-accent);
    }

    /* Alert Styling */
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    body.dark .alert-success {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.3);
    }

    .alert-success i {
        color: #28a745;
    }

    body.dark .alert-success i {
        color: #34d399;
    }
</style>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="categories-card">
                <div class="categories-card-header">
                    <h5 class="categories-card-title">
                        <i class="fas fa-tools"></i>
                        <span>Servicecategorieën</span>
                    </h5>
                    <p class="categories-subtitle">Selecteer alle categorieën die je kunt behandelen. Je ontvangt alleen opdrachten van deze categorieën.</p>
                </div>
                <div class="categories-card-body">
                    <form method="POST" action="{{ route('service-provider.categories.update') }}">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="group-card">
                                    <div class="group-card-header">
                                        <h6 class="group-card-title">
                                            <i class="fas fa-folder"></i>
                                            Beschikbare Service Types
                                        </h6>
                                    </div>
                                    <div class="group-card-body">
                                        <div class="row">
                                            @foreach ($categories as $cat)
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                               name="categories[]"
                                                               value="{{ $cat->id }}"
                                                               class="form-check-input"
                                                               id="category_{{ $cat->id }}"
                                                               {{ in_array($cat->id, $selected, true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="category_{{ $cat->id }}">
                                                            @if($cat->logo_url)
                                                                <img src="{{ $cat->logo_url }}" alt="{{ $cat->name }}" style="width: 20px; height: 20px; margin-right: 8px; vertical-align: middle;">
                                                            @endif
                                                            {{ $cat->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('dashboard') }}" class="btn-categories-outline">
                                <i class="fas fa-arrow-left"></i>
                                Terug naar Dashboard
                            </a>
                            <button type="submit" class="btn-categories">
                                <i class="fas fa-save"></i>
                                Categorieën Opslaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
 