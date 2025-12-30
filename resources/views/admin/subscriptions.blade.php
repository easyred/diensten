@extends('layouts.modern-dashboard')

@section('title', 'Subscriptions')

@section('page-title', 'Manage Subscriptions')

@section('sidebar-nav')
    @include('admin.partials.sidebar')
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Subscriptions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Starts</th>
                                    <th>Ends</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription->id }}</td>
                                        <td>{{ $subscription->user->full_name ?? $subscription->user->email }}</td>
                                        <td>{{ $subscription->plan_name }}</td>
                                        <td>{{ $subscription->currency }} {{ number_format($subscription->amount, 2) }}</td>
                                        <td>
                                            @if($subscription->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($subscription->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $subscription->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $subscription->starts_at ? $subscription->starts_at->format('Y-m-d') : 'N/A' }}</td>
                                        <td>{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $subscriptions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

