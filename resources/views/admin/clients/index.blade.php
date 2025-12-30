@extends('layouts.modern-dashboard')

@section('title', 'Klanten')

@section('page-title', 'Klanten')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Klanten</h4>
                    <p class="text-muted mb-0">Alle geregistreerde klanten in het systeem.</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary fs-6">{{ $clients->total() ?? 0 }} Totaal Klanten</span>
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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Naam</th>
                            <th>Email</th>
                            <th>Telefoon</th>
                            <th>WhatsApp</th>
                            <th>Locatie</th>
                            <th class="text-end">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $client)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $client->full_name }}</div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $client->email }}" class="text-decoration-none">
                                        {{ $client->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($client->phone)
                                        <a href="tel:{{ preg_replace('/\D+/', '', $client->phone) }}" class="text-decoration-none">
                                            {{ $client->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($client->whatsapp_number)
                                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $client->whatsapp_number) }}" 
                                           target="_blank" 
                                           class="text-decoration-none text-success">
                                            <i class="fab fa-whatsapp me-1"></i>
                                            {{ $client->whatsapp_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($client->postal_code || $client->city)
                                        <div class="small">
                                            @if($client->postal_code)
                                                <span class="badge bg-secondary">{{ $client->postal_code }}</span>
                                            @endif
                                            @if($client->city)
                                                <div class="text-muted">{{ $client->city }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.clients.show', $client) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="Bekijk">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.clients.edit', $client) }}" 
                                           class="btn btn-outline-secondary btn-sm" 
                                           title="Bewerk">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-info btn-sm" 
                                                title="Tele"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#teleModal{{ $client->id }}">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <form action="{{ route('admin.clients.destroy', $client) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Weet u zeker dat u deze klant wilt verwijderen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Verwijder">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <p>Geen klanten gevonden.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($clients->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $clients->links() }}
        </div>
    @endif

    <!-- Tele Modal for each client -->
    @foreach($clients as $client)
        <div class="modal fade" id="teleModal{{ $client->id }}" tabindex="-1" aria-labelledby="teleModalLabel{{ $client->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="teleModalLabel{{ $client->id }}">Create Tele Record - {{ $client->full_name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.tele.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $client->id }}">
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name{{ $client->id }}" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name{{ $client->id }}" name="name" value="{{ $client->full_name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="status{{ $client->id }}" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status{{ $client->id }}" name="status" required>
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
                                    <label for="postal_code{{ $client->id }}" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code{{ $client->id }}" name="postal_code" value="{{ $client->postal_code }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="city{{ $client->id }}" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city{{ $client->id }}" name="city" value="{{ $client->city }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="contacted_date{{ $client->id }}" class="form-label">Contacted Date</label>
                                    <input type="date" class="form-control" id="contacted_date{{ $client->id }}" name="contacted_date" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="message{{ $client->id }}" class="form-label">Message</label>
                                <textarea class="form-control" id="message{{ $client->id }}" name="message" rows="5" placeholder="Enter your message..."></textarea>
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

