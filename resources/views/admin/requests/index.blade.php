@extends('layouts.modern-dashboard')

@section('title', 'Serviceverzoeken')

@section('page-title', 'Serviceverzoeken')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Serviceverzoeken</h4>
                    <p class="text-muted mb-0">Alle serviceverzoeken van klanten.</p>
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
                            <th>ID</th>
                            <th>Klant</th>
                            <th>Probleem</th>
                            <th>Urgentie</th>
                            <th>Service Provider</th>
                            <th>Status</th>
                            <th>Datum</th>
                            <th class="text-end">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td>
                                    <div class="fw-medium">#{{ $request->id }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $request->customer->full_name ?? '—' }}</div>
                                    <div class="small text-muted">{{ $request->customer->email ?? '' }}</div>
                                </td>
                                <td>
                                    <div class="small">{{ Str::limit($request->problem ?? '—', 30) }}</div>
                                </td>
                                <td>
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
                                </td>
                                <td>
                                    @if($request->selectedPlumber)
                                        <div class="fw-medium">{{ $request->selectedPlumber->full_name }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
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
                                </td>
                                <td>
                                    <div class="small">{{ $request->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.requests.show', $request) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="Bekijk">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-tools fa-2x mb-2"></i>
                                        <p>Geen serviceverzoeken gevonden.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($requests->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $requests->links() }}
        </div>
    @endif
@endsection

