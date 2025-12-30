@extends('layouts.modern-dashboard')

@section('title', 'WhatsApp Stroomlijnen')

@section('page-title', 'WhatsApp Stroomlijnen')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">WhatsApp Stroomlijnen</h4>
                    <p class="text-muted mb-0">Beheer gesprekstromen per categorie (plumber, gardener, etc.)</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.flows.create', ['category_id' => $categoryId]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Nieuwe Stroom
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

    <!-- Category Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="mb-3">Filter op Categorie:</h6>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.flows.index') }}" 
                   class="btn {{ !$categoryId ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-globe me-1"></i>Alle Categorieën
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('admin.flows.index', ['category_id' => $cat->id]) }}" 
                       class="btn {{ $categoryId == $cat->id ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-tag me-1"></i>{{ $cat->name }}
                    </a>
                @endforeach
            </div>
            @if($categoryId)
                @php
                    $selectedCategory = $categories->firstWhere('id', $categoryId);
                @endphp
                <div class="mt-3">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Toont alleen stromen voor: <strong>{{ $selectedCategory->name ?? 'Onbekend' }}</strong>
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Categorie</th>
                            <th>Naam</th>
                            <th>Code</th>
                            <th>Ingangsleutelwoord</th>
                            <th>Doelrol</th>
                            <th>Status</th>
                            <th class="text-end">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flows as $flow)
                            <tr>
                                <td>
                                    @if($flow->category)
                                        <span class="badge bg-info">{{ $flow->category->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Algemeen</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $flow->name }}</div>
                                </td>
                                <td>
                                    <code class="text-xs">{{ $flow->code }}</code>
                                </td>
                                <td>
                                    @if($flow->entry_keyword)
                                        <span class="badge bg-info">{{ $flow->entry_keyword }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($flow->target_role)
                                        <span class="badge bg-secondary">{{ $flow->target_role }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($flow->is_active)
                                        <span class="badge bg-success">Actief</span>
                                    @else
                                        <span class="badge bg-secondary">Inactief</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.flows.nodes.index', $flow) }}" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="Beheer Knopen">
                                            <i class="fas fa-sitemap me-1"></i>
                                            Knopen
                                        </a>
                                        <a href="{{ route('admin.flows.edit', $flow) }}" 
                                           class="btn btn-outline-secondary btn-sm" 
                                           title="Bewerk Stroom">
                                            <i class="fas fa-edit me-1"></i>
                                            Bewerken
                                        </a>
                                        <form action="{{ route('admin.flows.destroy', $flow) }}" 
                                              method="POST" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Weet u zeker dat u deze stroom wilt verwijderen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm" 
                                                    title="Verwijder Stroom">
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
                                        <i class="fas fa-project-diagram fa-2x mb-2"></i>
                                        <p>
                                            @if($categoryId)
                                                Geen stromen gevonden voor deze categorie.
                                            @else
                                                Er zijn nog geen stromen aangemaakt.
                                            @endif
                                        </p>
                                        <a href="{{ route('admin.flows.create', ['category_id' => $categoryId]) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>
                                            Maak Eerste Stroom
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

    @if($flows->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $flows->links() }}
        </div>
    @endif
@endsection

