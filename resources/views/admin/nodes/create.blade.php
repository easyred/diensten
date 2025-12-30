@extends('layouts.modern-dashboard')

@section('title', 'Node Aanmaken')

@section('page-title', 'Node Aanmaken — ' . $flow->name)

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-plus me-2 text-success"></i>
                        Nieuwe Node Aanmaken — {{ $flow->name }}
                    </h4>
                    <a href="{{ route('admin.flows.nodes.index', $flow) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Terug naar Nodes
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.flows.nodes.store', $flow) }}" class="needs-validation" novalidate>
                        @csrf
                        @include('admin.nodes.partials.form', ['flow' => $flow, 'node' => $node])
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Creëer Node
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

