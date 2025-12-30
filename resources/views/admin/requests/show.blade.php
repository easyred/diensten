@extends('layouts.modern-dashboard')

@section('title', 'Serviceverzoek Details')

@section('page-title', 'Serviceverzoek Details')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Serviceverzoek #{{ $request->id }}</h4>
                    <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Terug
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Verzoek Informatie
                            </h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Status</label>
                            <div>
                                @php
                                    $statusLabels = [
                                        'broadcasting' => 'Uitzenden',
                                        'active' => 'Actief',
                                        'in_progress' => 'In Uitvoering',
                                        'completed' => 'Voltooid',
                                        'cancelled' => 'Geannuleerd'
                                    ];
                                    $statusClass = match($request->status) {
                                        'broadcasting' => 'info',
                                        'active' => 'success',
                                        'in_progress' => 'warning',
                                        'completed' => 'secondary',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ $statusLabels[$request->status] ?? $request->status }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Aangemaakt op</label>
                            <div>{{ $request->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Probleem</label>
                            <div class="fw-medium">{{ $request->problem ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Urgentie</label>
                            <div>
                                @php
                                    $urgencyLabels = [
                                        'high' => 'Hoog',
                                        'normal' => 'Normaal',
                                        'later' => 'Later'
                                    ];
                                    $urgencyClass = match($request->urgency) {
                                        'high' => 'danger',
                                        'normal' => 'warning',
                                        'later' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $urgencyClass }}">
                                    {{ $urgencyLabels[$request->urgency] ?? $request->urgency }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted small">Beschrijving</label>
                            <div>{{ $request->description ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Klant Informatie
                            </h5>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Naam</label>
                            <div class="fw-medium">{{ $request->customer->full_name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Email</label>
                            <div>
                                <a href="mailto:{{ $request->customer->email ?? '' }}" class="text-decoration-none">
                                    {{ $request->customer->email ?? '—' }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">WhatsApp</label>
                            <div>
                                @if($request->customer->whatsapp_number)
                                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $request->customer->whatsapp_number) }}" target="_blank" class="text-decoration-none text-success">
                                        <i class="fab fa-whatsapp me-1"></i>{{ $request->customer->whatsapp_number }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($request->selectedPlumber)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user-tie me-2"></i>Service Provider
                                </h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Naam</label>
                                <div class="fw-medium">{{ $request->selectedPlumber->full_name }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Email</label>
                                <div>
                                    <a href="mailto:{{ $request->selectedPlumber->email }}" class="text-decoration-none">
                                        {{ $request->selectedPlumber->email }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($request->status !== 'completed' && $request->status !== 'cancelled')
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-cog me-2"></i>Status Bijwerken
                                </h5>
                            </div>
                            <div class="col-12">
                                <form method="POST" action="{{ route('admin.requests.update-status', $request) }}" class="d-inline">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <select name="status" class="form-select" required>
                                                @foreach(\App\Models\WaRequest::getAvailableStatuses() as $value => $label)
                                                    <option value="{{ $value }}" {{ $request->status === $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Status Bijwerken
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('admin.requests.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Terug naar Lijst
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

