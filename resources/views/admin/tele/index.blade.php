@extends('layouts.modern-dashboard')

@section('title', 'Tele Records')

@section('page-title', 'Tele Records')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Tele Records</h4>
                    <p class="text-muted mb-0">Beheer tele records en communicatie.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.tele.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nieuwe Tele Record
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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Naam</th>
                            <th>Gebruiker</th>
                            <th>Postcode / Stad</th>
                            <th>Contact Datum</th>
                            <th>Status</th>
                            <th>Bericht</th>
                            <th class="text-end">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teleRecords as $record)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $record->name }}</div>
                                </td>
                                <td>
                                    @if($record->user)
                                        <div class="small">
                                            <div class="fw-medium">{{ $record->user->full_name }}</div>
                                            <div class="text-muted">
                                                @if($record->user->role === 'plumber')
                                                    <span class="badge bg-info">Plumber</span>
                                                @elseif($record->user->role === 'gardener')
                                                    <span class="badge bg-success">Gardener</span>
                                                @elseif($record->user->role === 'client')
                                                    <span class="badge bg-primary">Client</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->postal_code || $record->city)
                                        <div class="small">
                                            @if($record->postal_code)
                                                <span class="badge bg-secondary">{{ $record->postal_code }}</span>
                                            @endif
                                            @if($record->city)
                                                <div class="text-muted">{{ $record->city }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->contacted_date)
                                        {{ $record->contacted_date->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'Sent' => 'secondary',
                                            'Active' => 'info',
                                            'Called' => 'warning',
                                            'Interested' => 'primary',
                                            'Paid' => 'success'
                                        ];
                                        $color = $statusColors[$record->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $record->status }}</span>
                                </td>
                                <td>
                                    @if($record->message)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#messageModal{{ $record->id }}">
                                            <i class="fas fa-eye me-1"></i>
                                            Bekijk
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.tele.edit', $record) }}" 
                                           class="btn btn-outline-secondary btn-sm" 
                                           title="Bewerk">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.tele.destroy', $record) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Weet u zeker dat u dit record wilt verwijderen?')">
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

                            <!-- Message Modal -->
                            <div class="modal fade" id="messageModal{{ $record->id }}" tabindex="-1" aria-labelledby="messageModalLabel{{ $record->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="messageModalLabel{{ $record->id }}">Tele Record Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Naam:</strong>
                                                    <p>{{ $record->name }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Status:</strong>
                                                    <p>
                                                        @php
                                                            $statusColors = [
                                                                'Sent' => 'secondary',
                                                                'Active' => 'info',
                                                                'Called' => 'warning',
                                                                'Interested' => 'primary',
                                                                'Paid' => 'success'
                                                            ];
                                                            $color = $statusColors[$record->status] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $color }}">{{ $record->status }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <strong>Bericht:</strong>
                                                <div class="border rounded p-3 mt-2" style="min-height: 100px;">
                                                    {!! nl2br(e($record->message ?? 'Geen bericht')) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sluiten</button>
                                            <a href="{{ route('admin.tele.edit', $record) }}" class="btn btn-primary">Bewerken</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-phone fa-2x mb-2"></i>
                                        <p>Geen tele records gevonden.</p>
                                        <a href="{{ route('admin.tele.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus me-2"></i>
                                            Maak Eerste Record
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

    @if($teleRecords->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $teleRecords->links() }}
        </div>
    @endif
@endsection

