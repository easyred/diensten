@extends('layouts.modern-dashboard')

@section('title', 'Creëer Stroom')

@section('page-title', 'Creëer Stroom')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-dark">
                            <i class="fas fa-plus me-2 text-success"></i>
                            Nieuwe Stroom Creëren
                        </h4>
                        <a href="{{ route('admin.flows.index', ['category_id' => $flow->category_id]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Terug naar Stromen
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.flows.store') }}" class="needs-validation" novalidate>
                        @csrf
                        @include('admin.flows.partials.form', ['flow' => $flow, 'categories' => $categories])
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Creëer Stroom
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

