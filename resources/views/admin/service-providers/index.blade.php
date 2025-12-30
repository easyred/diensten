@extends('layouts.modern-dashboard')

@section('title', 'Service Providers')

@section('page-title', 'Service Providers')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Service Providers</h4>
                    <p class="text-muted mb-0">Beheer service providers (plumbers, gardeners, etc.)</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary fs-6">{{ $serviceProviders->total() ?? 0 }} Totaal</span>
                    <a href="{{ route('admin.service-providers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Voeg Service Provider Toe
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Category Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="mb-3">Filter op Categorie:</h6>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.service-providers.index') }}" 
                   class="btn {{ !$categoryId ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-globe me-1"></i>Alle Categorieën
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('admin.service-providers.index', ['category_id' => $cat->id]) }}" 
                       class="btn {{ $categoryId == $cat->id ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-tag me-1"></i>{{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Naam</th>
                            <th>Bedrijf</th>
                            <th>Categorieën</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>Locatie</th>
                            <th class="text-end">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceProviders as $provider)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $provider->full_name }}</div>
                                    <div class="small text-muted">
                                        <span class="badge bg-info">{{ ucfirst($provider->role) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        {{ $provider->company_name ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    @if($provider->categories->count() > 0)
                                        @foreach($provider->categories as $cat)
                                            <span class="badge bg-secondary me-1">{{ $cat->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="mailto:{{ $provider->email }}" class="text-decoration-none">
                                        {{ $provider->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($provider->whatsapp_number)
                                        <div class="small">
                                            <i class="fab fa-whatsapp me-1 text-success"></i>
                                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $provider->whatsapp_number) }}" target="_blank" class="text-decoration-none">
                                                {{ $provider->whatsapp_number }}
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        @if($provider->postal_code || $provider->city)
                                            {{ $provider->postal_code ?? '' }} {{ $provider->city ?? '' }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.service-providers.show', $provider) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="Bekijk">
                                            <i class="fas fa-eye me-1"></i>
                                            Bekijk
                                        </a>
                                        <a href="{{ route('admin.service-providers.edit', $provider) }}" 
                                           class="btn btn-outline-secondary btn-sm" 
                                           title="Bewerken">
                                            <i class="fas fa-edit me-1"></i>
                                            Bewerken
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-info btn-sm" 
                                                title="Tele"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#teleModal{{ $provider->id }}">
                                            <i class="fas fa-phone me-1"></i>
                                            Tele
                                        </button>
                                        <form action="{{ route('admin.service-providers.destroy', $provider) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Weet je zeker dat je deze service provider wilt verwijderen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Verwijderen">
                                                <i class="fas fa-trash me-1"></i>
                                                Verwijderen
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-user-tie fa-2x mb-2"></i>
                                        <p>Geen service providers gevonden.</p>
                                        <a href="{{ route('admin.service-providers.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            Voeg Eerste Service Provider Toe
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($serviceProviders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $serviceProviders->links() }}
        </div>
    @endif

    <!-- Tele Modal for each provider -->
    @foreach($serviceProviders as $provider)
        <div class="modal fade" id="teleModal{{ $provider->id }}" tabindex="-1" aria-labelledby="teleModalLabel{{ $provider->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="teleModalLabel{{ $provider->id }}">Create Tele Record - {{ $provider->full_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.tele.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $provider->id }}">
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name{{ $provider->id }}" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name{{ $provider->id }}" name="name" value="{{ $provider->full_name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="status{{ $provider->id }}" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status{{ $provider->id }}" name="status" required>
                                        <option value="Sent">Sent</option>
                                        <option value="Active">Active</option>
                                        <option value="Called">Called</option>
                                        <option value="Interested">Interested</option>
                                        <option value="Paid">Paid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="postal_code{{ $provider->id }}" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code{{ $provider->id }}" name="postal_code" value="{{ $provider->postal_code }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="city{{ $provider->id }}" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city{{ $provider->id }}" name="city" value="{{ $provider->city }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="contacted_date{{ $provider->id }}" class="form-label">Contacted Date</label>
                                    <input type="date" class="form-control" id="contacted_date{{ $provider->id }}" name="contacted_date" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="message{{ $provider->id }}" class="form-label">Message</label>
                                <textarea class="form-control" id="message{{ $provider->id }}" name="message" rows="5" placeholder="Enter your message..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Tele Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

