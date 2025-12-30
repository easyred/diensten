@extends('layouts.modern-dashboard')

@section('title', 'Nodes: ' . $flow->name)

@section('page-title', 'Nodes — ' . $flow->name)

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Nodes — {{ $flow->name }}</h4>
                    <p class="text-muted mb-0">Definieer stappen, opties en routering naar de volgende node voor dynamische gesprekken.</p>
                </div>
                <div>
                    <a href="{{ route('admin.flows.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>
                        Terug naar Stromen
                    </a>
                    <a href="{{ route('admin.flows.nodes.create', $flow) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nieuwe Node
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
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
                            <th width="80">Sorteer</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Titel</th>
                            <th>Voorbeeld</th>
                            <th class="text-end">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nodes as $n)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $n->sort }}</span>
                                </td>
                                <td>
                                    <code class="text-xs">{{ $n->code }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $n->type }}</span>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $n->title }}</div>
                                </td>
                                <td>
                                    <div class="bg-light p-2 rounded text-xs" style="max-width: 300px; overflow: hidden;">
                                        {{ Str::limit(($n->body ?? ''), 120) }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.flows.nodes.edit', [$flow, $n]) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="Bewerk Node">
                                            <i class="fas fa-edit me-1"></i>
                                            Bewerken
                                        </a>
                                        <form action="{{ route('admin.flows.nodes.destroy', [$flow, $n]) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Weet je zeker dat je deze node wilt verwijderen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Verwijder Node">
                                                <i class="fas fa-trash me-1"></i>
                                                Verwijderen
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-sitemap fa-2x mb-2"></i>
                                        <p>Er zijn nog geen nodes gemaakt voor deze stroom.</p>
                                        <a href="{{ route('admin.flows.nodes.create', $flow) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            Maak Eerste Node
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
@endsection

