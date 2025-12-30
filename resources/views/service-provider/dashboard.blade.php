
@extends('layouts.dashboard-top-header')

@section('title', 'Tuinman Dashboard')

@push('styles')
<style>
    /* Force style updates - cache buster */
    /* Stats Cards */
    .stats-card {
        background: var(--card-bg-light);
        border-radius: 16px;
        border: 1px solid var(--border-light);
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        transition: box-shadow 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stats-card::before {
        display: none; /* Remove animated top border */
    }

    .stats-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        /* Removed transform animation */
    }

    .stats-card:hover::before {
        display: none;
    }

    body.dark .stats-card {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    body.dark .stats-card:hover {
        box-shadow: 0 8px 28px rgba(16, 185, 129, 0.15), 0 2px 8px rgba(0, 0, 0, 0.4);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .stats-card .stats-icon-circle {
        width: 48px !important;
        height: 48px !important;
        border-radius: 12px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem !important;
        color: white !important;
        margin-right: 1rem !important;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important;
        transition: none; /* Remove animations */
        flex-shrink: 0;
        position: relative;
    }

    .stats-card .flex-grow-1 {
        flex: 1;
        min-width: 0;
    }

    .stats-card:hover .stats-icon-circle {
        /* Removed animations for cleaner look */
    }

    /* Black/Dark Icons - Matching Screenshot - Force override with maximum specificity */
    .stats-card .stats-icon-circle.stats-icon-blue,
    .stats-card .stats-icon-circle.stats-icon-yellow,
    .stats-card .stats-icon-circle.stats-icon-green,
    .stats-card .stats-icon-circle.stats-icon-light-green,
    div.stats-card div.stats-icon-circle.stats-icon-blue,
    div.stats-card div.stats-icon-circle.stats-icon-yellow,
    div.stats-card div.stats-icon-circle.stats-icon-green,
    div.stats-card div.stats-icon-circle.stats-icon-light-green {
        background: #1f2937 !important;
        background-color: #1f2937 !important;
        background-image: none !important;
    }

    body.dark .stats-card .stats-icon-circle.stats-icon-blue,
    body.dark .stats-card .stats-icon-circle.stats-icon-yellow,
    body.dark .stats-card .stats-icon-circle.stats-icon-green,
    body.dark .stats-card .stats-icon-circle.stats-icon-light-green,
    body.dark div.stats-card div.stats-icon-circle.stats-icon-blue,
    body.dark div.stats-card div.stats-icon-circle.stats-icon-yellow,
    body.dark div.stats-card div.stats-icon-circle.stats-icon-green,
    body.dark div.stats-card div.stats-icon-circle.stats-icon-light-green {
        background: #374151 !important;
        background-color: #374151 !important;
        background-image: none !important;
    }

    .stats-label {
        color: #4b5563;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        text-transform: none;
        letter-spacing: 0.3px;
    }

    body.dark .stats-label {
        color: #9ca3af;
    }

    body.dark .stats-label {
        color: var(--text-secondary-dark);
    }

    body.dark .fw-semibold {
        color: var(--text-primary-dark) !important;
    }

    .stats-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--text-primary-light);
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    body.dark .stats-number {
        color: var(--text-primary-dark);
    }

    /* Tabs - Green Color Scheme */
    .dashboard-tabs {
        display: flex;
        gap: 0.5rem;
        padding: 0 1.75rem;
        border-bottom: 2px solid var(--border-light);
        background-color: transparent;
    }

    body.dark .dashboard-tabs {
        border-bottom-color: var(--border-dark);
        background-color: transparent;
    }

    .dashboard-tab {
        padding: 1rem 1.5rem;
        background: transparent;
        border: none;
        color: var(--text-secondary-light);
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: border-color 0.2s ease, color 0.2s ease;
        position: relative;
        margin-bottom: -2px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-radius: 0;
    }

    .dashboard-tab .badge {
        background-color: transparent !important;
        color: var(--text-secondary-light) !important;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0;
        border-radius: 0;
        transition: all 0.3s ease;
        min-width: auto;
        text-align: center;
        margin-left: 0.25rem;
    }

    body.dark .dashboard-tab .badge {
        color: var(--text-secondary-dark) !important;
    }

    .dashboard-tab.active .badge {
        background: transparent !important;
        color: var(--text-primary-light) !important;
        box-shadow: none;
    }

    body.dark .dashboard-tab.active .badge {
        color: var(--text-primary-dark) !important;
    }

    body.dark .dashboard-tab {
        color: var(--text-secondary-dark);
    }

    .dashboard-tab i {
        font-size: 0.875rem;
        transition: color 0.2s ease;
    }

    .dashboard-tab:hover {
        color: var(--text-primary-light);
        background-color: rgba(0, 0, 0, 0.02);
    }

    .dashboard-tab:hover i {
        color: var(--text-primary-light);
        /* Removed scale animation */
    }

    body.dark .dashboard-tab:hover {
        color: var(--text-primary-dark);
        background-color: rgba(255, 255, 255, 0.05);
    }

    body.dark .dashboard-tab:hover i {
        color: var(--text-primary-dark);
    }

    .dashboard-tab.active {
        color: var(--text-primary-light) !important;
        border-bottom-color: #1f2937 !important;
        border-bottom: 3px solid #1f2937 !important;
        background: transparent !important;
        font-weight: 600;
    }

    .dashboard-tab.active i {
        color: var(--text-primary-light) !important;
        /* Removed scale animation */
    }

    body.dark .dashboard-tab.active {
        color: var(--text-primary-dark) !important;
        border-bottom-color: #ffffff !important;
    }

    body.dark .dashboard-tab.active i {
        color: var(--text-primary-dark) !important;
    }

    /* Jobs Table */
    .jobs-table {
        width: 100%;
        color: var(--text-primary-light);
        border-collapse: separate;
        border-spacing: 0;
    }

    body.dark .jobs-table {
        color: var(--text-primary-dark);
    }

    .jobs-table thead {
        background-color: transparent;
    }

    .jobs-table th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary-light);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border-light);
        background-color: transparent;
    }

    body.dark .jobs-table th {
        color: var(--text-secondary-dark);
        border-bottom-color: var(--border-dark);
    }

    .jobs-table td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-light);
        transition: background-color 0.2s ease;
        background-color: transparent;
    }

    body.dark .jobs-table td {
        border-bottom-color: var(--border-dark);
    }

    .jobs-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .jobs-table tbody tr {
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
    }

    .jobs-table tbody tr:hover {
        background-color: rgba(16, 185, 129, 0.06);
        border-left-color: var(--green-accent);
    }

    body.dark .jobs-table tbody tr:hover {
        background-color: rgba(16, 185, 129, 0.12);
    }

    .job-title {
        font-weight: 600;
        color: var(--text-primary-light);
        margin-bottom: 0.25rem;
    }

    body.dark .job-title {
        color: var(--text-primary-dark);
    }

    .job-subtitle {
        font-size: 0.875rem;
        color: var(--text-secondary-light);
    }

    body.dark .job-subtitle {
        color: var(--text-secondary-dark);
    }

    .location-text {
        color: var(--text-primary-light);
    }

    body.dark .location-text {
        color: var(--text-primary-dark);
    }

    .posted-text {
        color: var(--text-secondary-light);
        font-size: 0.875rem;
    }

    body.dark .posted-text {
        color: var(--text-secondary-dark);
    }

    /* Buttons - Unified Green Color Scheme - Simplified animations */
    .btn-accept,
    .btn-primary-custom,
    .btn-update-profile {
        background: linear-gradient(135deg, var(--green-accent) 0%, #059669 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background 0.2s ease, box-shadow 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        position: relative;
        overflow: hidden;
    }

    .btn-accept::before,
    .btn-primary-custom::before,
    .btn-update-profile::before {
        display: none; /* Remove shimmer animation */
    }

    .btn-accept:hover,
    .btn-primary-custom:hover,
    .btn-update-profile:hover {
        background: linear-gradient(135deg, #059669 0%, var(--green-accent) 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        /* Removed transform animation */
    }

    .btn-accept:active,
    .btn-primary-custom:active,
    .btn-update-profile:active {
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
    }

    .btn-accept i,
    .btn-primary-custom i,
    .btn-update-profile i {
        font-size: 0.875rem;
    }


    .info-icon {
        color: var(--green-accent);
        margin-left: 0.5rem;
    }

    /* Business Location Warning */
    .location-warning {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 12px;
        color: var(--text-primary-light);
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.1);
    }

    body.dark .location-warning {
        color: var(--text-primary-dark);
    }

    .warning-icon {
        font-size: 1.5rem;
        color: #f59e0b;
    }

    /* Scrollbar Styling */
    .jobs-table-container {
        max-height: 500px;
        overflow-y: auto;
    }

    .jobs-table-container::-webkit-scrollbar {
        width: 8px;
    }

    .jobs-table-container::-webkit-scrollbar-track {
        background: var(--card-bg-light);
    }

    body.dark .jobs-table-container::-webkit-scrollbar-track {
        background: var(--card-bg-dark);
    }

    .jobs-table-container::-webkit-scrollbar-thumb {
        background: var(--border-light);
        border-radius: 4px;
    }

    body.dark .jobs-table-container::-webkit-scrollbar-thumb {
        background: var(--border-dark);
    }

    .jobs-table-container::-webkit-scrollbar-thumb:hover {
        background: var(--green-accent);
    }

    /* Professional Quick Actions Buttons - Unified Design */
    .quick-action-btn {
        background: var(--card-bg-light);
        border: 1px solid var(--border-light);
        color: var(--text-primary-light);
        padding: 0.875rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.75rem;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .quick-action-btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        color: var(--text-primary-light);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        transform: none;
    }

    .quick-action-btn i {
        color: #6b7280;
        font-size: 0.875rem;
        width: 18px;
        text-align: center;
    }

    .quick-action-btn:hover i {
        color: var(--text-primary-light);
    }

    body.dark .quick-action-btn {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        color: var(--text-primary-dark);
    }

    body.dark .quick-action-btn:hover {
        background: #3a3f47;
        border-color: #4b5563;
        color: var(--text-primary-dark);
    }

    body.dark .quick-action-btn i {
        color: #9ca3af;
    }

    body.dark .quick-action-btn:hover i {
        color: var(--text-primary-dark);
    }

    /* Account Information Styling */
    .account-info-label {
        color: #6b7280;
        font-size: 0.8125rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .account-info-value {
        color: var(--text-primary-light);
        font-size: 0.9375rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
    }

    body.dark .account-info-label {
        color: #9ca3af;
    }

    body.dark .account-info-value {
        color: var(--text-primary-dark);
    }

    /* Remove colored icons from card titles */
    .dashboard-card-title i {
        color: #6b7280 !important;
    }

    body.dark .dashboard-card-title i {
        color: #9ca3af !important;
    }

    /* Upgrade Modal Styling */
    .modal-content {
        background: var(--card-bg-light);
        border: 1px solid var(--border-light);
    }

    body.dark .modal-content {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
    }

    .modal-header {
        background: transparent;
    }

    .modal-title {
        color: var(--text-primary-light);
    }

    body.dark .modal-title {
        color: var(--text-primary-dark);
    }

    .btn-close {
        filter: invert(0.5);
    }

    body.dark .btn-close {
        filter: invert(0.8);
    }
    
    /* Dark Mode Text Fixes - Override all dark text colors */
    body.dark h5,
    body.dark h6,
    body.dark .h5,
    body.dark .h6 {
        color: #f1f5f9 !important;
    }
    
    body.dark strong {
        color: #f1f5f9 !important;
    }
    
    body.dark p {
        color: #e2e8f0 !important;
    }
    
    body.dark span {
        color: inherit;
    }
    
    body.dark .text-center {
        color: #e2e8f0 !important;
    }
    
    body.dark .text-center p {
        color: #e2e8f0 !important;
    }
    
    body.dark .text-center h6 {
        color: #f1f5f9 !important;
    }
    
    /* Fix inline styles with var(--text-primary-light) in dark mode */
    body.dark [style*="color: var(--text-primary-light)"],
    body.dark [style*="color:var(--text-primary-light)"] {
        color: var(--text-primary-dark) !important;
    }
    
    body.dark [style*="color: var(--text-secondary-light)"],
    body.dark [style*="color:var(--text-secondary-light)"] {
        color: var(--text-secondary-dark) !important;
    }
    
    /* Fix specific elements that might have dark text */
    body.dark .dashboard-card-body h5,
    body.dark .dashboard-card-body h6,
    body.dark .dashboard-card-body p,
    body.dark .dashboard-card-body span,
    body.dark .dashboard-card-body div {
        color: inherit;
    }
    
    body.dark .dashboard-card-body [style*="color: var(--text-primary-light)"] {
        color: var(--text-primary-dark) !important;
    }
    
    body.dark .dashboard-card-body [style*="color: var(--text-secondary-light)"] {
        color: var(--text-secondary-dark) !important;
    }
    
    /* Fix location warning text */
    body.dark .location-warning {
        color: #f1f5f9 !important;
    }
    
    body.dark .location-warning strong {
        color: #f1f5f9 !important;
    }
    
    /* Fix modal text */
    body.dark .modal-body h6,
    body.dark .modal-body p {
        color: #e2e8f0 !important;
    }
    
    /* Fix coverage area cards */
    body.dark [style*="color: var(--text-primary-light)"] {
        color: var(--text-primary-dark) !important;
    }
    
    body.dark [style*="color: var(--text-secondary-light)"] {
        color: var(--text-secondary-dark) !important;
    }
    
    /* Fix all text in dashboard cards */
    body.dark .dashboard-card * {
        color: inherit;
    }
    
    body.dark .dashboard-card h5,
    body.dark .dashboard-card h6 {
        color: #f1f5f9 !important;
    }
    
    body.dark .dashboard-card p {
        color: #e2e8f0 !important;
    }
    
    body.dark .dashboard-card small {
        color: #cbd5e1 !important;
    }
    
    /* Fix account info section */
    body.dark .account-info-value {
        color: #f1f5f9 !important;
    }
    
    /* Fix category badges */
    body.dark [style*="color: var(--text-primary-light)"] span {
        color: var(--text-primary-dark) !important;
    }
    
    /* Fix schedule times */
    body.dark [style*="color: var(--text-primary-light)"],
    body.dark [style*="color: var(--text-secondary-light)"] {
        color: var(--text-primary-dark) !important;
    }
    
    body.dark [style*="color: var(--text-secondary-light)"] {
        color: var(--text-secondary-dark) !important;
    }
    
    /* Additional dark mode text fixes for specific elements */
    body.dark .dashboard-card-body > * {
        color: #e2e8f0;
    }
    
    body.dark .dashboard-card-body h5,
    body.dark .dashboard-card-body h6 {
        color: #f1f5f9 !important;
    }
    
    body.dark .dashboard-card-body p,
    body.dark .dashboard-card-body div,
    body.dark .dashboard-card-body span:not(.badge) {
        color: #e2e8f0 !important;
    }
    
    /* Fix all text that might be using dark colors */
    body.dark .text-muted,
    body.dark .text-secondary {
        color: #9ca3af !important;
    }
    
    body.dark .text-dark {
        color: #f1f5f9 !important;
    }
    
    /* Fix inline style attributes */
    body.dark [style*="color: #1f2937"],
    body.dark [style*="color:#1f2937"],
    body.dark [style*="color: #6b7280"],
    body.dark [style*="color:#6b7280"] {
        color: #e2e8f0 !important;
    }
    
</style>
@endpush

@section('content')
<!-- Stats Cards Row -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon-circle stats-icon-blue">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-label">Totaal Klussen</div>
                    <div class="stats-number">{{ $stats['total_requests'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon-circle stats-icon-yellow">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-label">Actieve Klussen</div>
                    <div class="stats-number">{{ $stats['active_requests'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon-circle stats-icon-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-label">Voltooide Klussen</div>
                    <div class="stats-number">{{ $stats['completed_requests'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon-circle stats-icon-purple">
                    <i class="fas fa-star"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="stats-label">Gemiddelde Beoordeling</div>
                    <div class="stats-number">
                        @if(isset($stats['average_rating']) && $stats['average_rating'])
                            {{ number_format($stats['average_rating'], 1) }}/5
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="stats-label" style="font-size: 0.75rem; margin-top: 0.25rem;">
                        {{ $stats['total_reviews'] ?? 0 }} beoordelingen
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account Type Section -->
<div class="dashboard-card mb-4">
    <div class="dashboard-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 style="margin: 0; color: var(--text-primary-light); font-weight: 600; font-size: 1.125rem;">
                    Hallo {{ Auth::user()->full_name ?? Auth::user()->email }}
                </h5>
                <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 1rem;">
                    <span style="color: var(--text-secondary-light); font-size: 0.9375rem;">
                        Accounttype: <strong style="color: var(--text-primary-light);">{{ ucfirst(Auth::user()->subscription_plan ?? 'Basis') }}</strong>
                    </span>
                </div>
            </div>
            <button type="button" class="btn-accept" data-bs-toggle="modal" data-bs-target="#upgradeModal" style="padding: 0.75rem 1.5rem;">
                <i class="fas fa-arrow-up me-2"></i>
                Upgrade
            </button>
        </div>
    </div>
</div>

<!-- Upgrade Modal -->
<div class="modal fade" id="upgradeModal" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-light); padding: 1.5rem;">
                <h5 class="modal-title" id="upgradeModalLabel" style="font-weight: 600; color: var(--text-primary-light);">
                    <i class="fas fa-arrow-up me-2" style="color: var(--green-accent);"></i>
                    Upgrade uw account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="text-center">
                    <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-phone-alt fa-2x" style="color: var(--green-accent);"></i>
                    </div>
                    <h6 style="font-weight: 600; color: var(--text-primary-light); margin-bottom: 1rem; font-size: 1.125rem;">
                        Neem contact op met de verkoop
                    </h6>
                    <p style="color: var(--text-secondary-light); margin-bottom: 1.5rem; font-size: 0.9375rem;">
                        Om uw account te upgraden, neem contact op met ons verkoopteam:
                    </p>
                    <div style="margin-bottom: 1.5rem;">
                        <a href="tel:+32490458009" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; background: linear-gradient(135deg, var(--green-accent) 0%, #059669 100%); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 1rem; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25); transition: all 0.2s ease;">
                            <i class="fas fa-phone"></i>
                            +32 490 45 80 09
                        </a>
                    </div>
                    <p style="color: var(--text-secondary-light); font-size: 0.9375rem; margin: 0;">
                        of
                    </p>
                    <div style="margin-top: 1rem;">
                        <button type="button" class="btn-accept" style="padding: 0.875rem 1.5rem; font-size: 0.9375rem;" onclick="initiateChat()">
                            <i class="fas fa-comments me-2"></i>
                            Start Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Coverage Areas and Opening Times in One Row -->
<div class="row mb-4">
    <!-- Coverage Areas Column -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    WerkZone
                </h5>
                <a href="{{ route('service-provider.coverage.index') }}" class="btn-accept" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                    <i class="fas fa-plus me-2"></i>
                    Beheer WerkZone
                </a>
            </div>
            <div class="dashboard-card-body">
                @if(Auth::user()->coverages->count() > 0)
                    @php
                        $user = Auth::user();
                        $userCity = $user->city;
                        $userCoords = null;
                        
                        // Get user's city coordinates
                        if ($userCity) {
                            $userCoords = \Illuminate\Support\Facades\DB::table('postal_codes')
                                ->select('Latitude', 'Longitude')
                                ->where('Plaatsnaam_NL', $userCity)
                                ->whereNotNull('Latitude')
                                ->whereNotNull('Longitude')
                                ->first();
                        }
                    @endphp
                    <div style="max-height: 400px; overflow-y: auto;">
                        @foreach(Auth::user()->coverages->take(10) as $coverage)
                            @php
                                $distance = null;
                                $distanceText = '';
                                
                                // Calculate distance if user has coordinates
                                if ($userCoords) {
                                    // Get municipality center coordinates
                                    $municipalityCoords = DB::table('postal_codes')
                                        ->select('Latitude', 'Longitude')
                                        ->where('Hoofdgemeente', $coverage->hoofdgemeente)
                                        ->whereNotNull('Latitude')
                                        ->whereNotNull('Longitude')
                                        ->first();
                                    
                                    if ($municipalityCoords) {
                                        // Calculate distance using Haversine formula
                                        $distanceResult = \Illuminate\Support\Facades\DB::selectOne('
                                            SELECT (6371 * acos(cos(radians(?)) * cos(radians(?)) * 
                                            cos(radians(?) - radians(?)) + sin(radians(?)) * 
                                            sin(radians(?)))) AS distance
                                        ', [
                                            $userCoords->Latitude, 
                                            $municipalityCoords->Latitude,
                                            $municipalityCoords->Longitude, 
                                            $userCoords->Longitude,
                                            $userCoords->Latitude, 
                                            $municipalityCoords->Latitude
                                        ]);
                                        
                                        $distance = $distanceResult->distance ?? 0;
                                        
                                        // Format distance text
                                        if ($distance < 0.1) {
                                            $distanceText = 'Uw locatie';
                                        } else {
                                            $distanceText = number_format($distance, 1) . 'km away';
                                        }
                                    }
                                }
                                
                                // Fallback if no distance calculated
                                if (!$distanceText) {
                                    $distanceText = 'Uw gemeente';
                                }
                            @endphp
                            <div style="display: flex; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--border-light);">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background-color: var(--green-accent); display: flex; align-items: center; justify-content: center; color: white; margin-right: 1rem; flex-shrink: 0;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; color: var(--text-primary-light); font-size: 0.9375rem; margin-bottom: 0.25rem;">
                                        {{ $coverage->hoofdgemeente }}
                                    </div>
                                    <div style="color: var(--text-secondary-light); font-size: 0.8125rem;">
                                        @if($coverage->coverage_type === 'city')
                                            <i class="fas fa-building me-1"></i>{{ $coverage->city }}
                                        @else
                                            <i class="fas fa-ruler me-1"></i>{{ $distanceText }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if(Auth::user()->coverages->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('service-provider.coverage.index') }}" class="btn-accept" style="padding: 0.5rem 1.5rem;">
                                Bekijk Alle {{ Auth::user()->coverages->count() }} gebieden
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-map-marker-alt fa-3x" style="color: var(--text-secondary-light); margin-bottom: 1rem;"></i>
                        <h6 style="color: var(--text-secondary-light);">Geen dekkinggebieden ingesteld</h6>
                        <p style="color: var(--text-secondary-light); margin-bottom: 1.5rem;">Voeg dekkinggebieden toe om klusverzoeken in uw omgeving te ontvangen.</p>
                        <a href="{{ route('service-provider.coverage.index') }}" class="btn-accept">
                            <i class="fas fa-plus me-2"></i>
                            Dekkinggebieden Toevoegen
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Opening Times Column -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header d-flex justify-content-between align-items-center">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-clock"></i>
                    Openingstijden
                </h5>
                <a href="{{ route('service-provider.schedule.index') }}" class="btn-accept" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                    <i class="fas fa-edit me-2"></i>
                    Beheren
                </a>
            </div>
            <div class="dashboard-card-body">
                @php
                    $scheduleData = $schedule ? $schedule->schedule_data : [];
                    $days = ['monday' => 'Maandag', 'tuesday' => 'Dinsdag', 'wednesday' => 'Woensdag', 
                             'thursday' => 'Donderdag', 'friday' => 'Vrijdag', 'saturday' => 'Zaterdag', 'sunday' => 'Zondag'];
                @endphp
                @if($schedule && !empty($scheduleData))
                    <div style="max-height: 400px; overflow-y: auto;">
                        @foreach($days as $key => $label)
                            @php
                                $day = $scheduleData[$key] ?? ['mode' => 'open24', 'split' => ['o1' => '09:00', 'c1' => '12:00', 'o2' => '13:30', 'c2' => '19:00'], 'full' => ['o' => '09:00', 'c' => '19:00']];
                                $mode = $day['mode'] ?? 'open24';
                                $timeDisplay = '';
                                
                                switch($mode) {
                                    case 'closed':
                                        $timeDisplay = '<span style="color: #dc3545;">Gesloten</span>';
                                        break;
                                    case 'open24':
                                        $timeDisplay = '<span style="color: #28a745;">24 Uur</span>';
                                        break;
                                    case 'split':
                                        $split = $day['split'] ?? ['o1' => '09:00', 'c1' => '12:00', 'o2' => '13:30', 'c2' => '19:00'];
                                        $timeDisplay = $split['o1'] . ' - ' . $split['c1'] . ', ' . $split['o2'] . ' - ' . $split['c2'];
                                        break;
                                    case 'fullday':
                                        $full = $day['full'] ?? ['o' => '09:00', 'c' => '19:00'];
                                        $timeDisplay = $full['o'] . ' - ' . $full['c'];
                                        break;
                                }
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--border-light);">
                                <span style="font-weight: 500; color: var(--text-primary-light); font-size: 0.875rem;">{{ $label }}</span>
                                <span style="color: var(--text-secondary-light); font-size: 0.875rem;">{!! $timeDisplay !!}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-2x" style="color: var(--text-secondary-light); margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p style="color: var(--text-secondary-light); margin-bottom: 1rem;">Geen rooster ingesteld</p>
                        <a href="{{ route('service-provider.schedule.index') }}" class="btn-accept" style="padding: 0.5rem 1.5rem; font-size: 0.875rem;">
                            <i class="fas fa-plus me-2"></i>
                            Rooster Instellen
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Business Location Section -->
@if(!Auth::user()->address || !Auth::user()->city)
<div class="dashboard-card">
    <div class="dashboard-card-body">
        <div class="location-warning">
            <i class="fas fa-exclamation-triangle warning-icon"></i>
            <div style="flex: 1;">
                <strong>Locatie niet ingesteld.</strong> Werk uw profiel bij om uw bedrijfs locatie in te stellen.
            </div>
            <a href="{{ route('profile.edit') }}" class="btn-update-profile">
                <i class="fas fa-pencil-alt"></i>
                Profiel Bijwerken
            </a>
        </div>
    </div>
</div>
@endif


<!-- Quick Actions -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-bolt"></i>
                    <span>Snel Acties</span>
                </h5>
            </div>
            <div class="dashboard-card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('service-provider.coverage.index') }}" class="quick-action-btn">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Beheer WerkZone</span>
                    </a>
                    <a href="{{ route('service-provider.schedule.index') }}" class="quick-action-btn">
                        <i class="fas fa-clock"></i>
                        <span>Beheer Rooster</span>
                    </a>
                    <a href="{{ route('support') }}" class="quick-action-btn">
                        <i class="fas fa-headset"></i>
                        <span>Neem Contact op met Ondersteuning</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="quick-action-btn">
                        <i class="fas fa-user-cog"></i>
                        <span>Profiel Bijwerken</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h5 class="dashboard-card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Accountinformatie</span>
                </h5>
            </div>
            <div class="dashboard-card-body">
                <div class="row">
                    <div class="col-6">
                        <p class="account-info-label">Naam</p>
                        <p class="account-info-value">{{ Auth::user()->full_name ?? 'Niet ingesteld' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="account-info-label">E-mail</p>
                        <p class="account-info-value">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="col-6">
                        <p class="account-info-label">Stad</p>
                        <p class="account-info-value">{{ Auth::user()->city ?? 'Niet ingesteld' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="account-info-label">WhatsApp</p>
                        <p class="account-info-value">{{ Auth::user()->whatsapp_number ? format_phone_number(Auth::user()->whatsapp_number) : 'Niet ingesteld' }}</p>
                    </div>
                    @if(Auth::user()->company_name)
                        <div class="col-6">
                            <p class="account-info-label">Bedrijf</p>
                            <p class="account-info-value">{{ Auth::user()->company_name }}</p>
                        </div>
                    @endif
                    @if(Auth::user()->btw_number)
                        <div class="col-6">
                            <p class="account-info-label">BTW-Nummer</p>
                            <p class="account-info-value">{{ Auth::user()->btw_number }}</p>
                        </div>
                    @endif
                    @if(isset($stats['total_services']) && $stats['total_services'] > 0)
                        <div class="col-6">
                            <p class="account-info-label">Totaal Services</p>
                            <p class="account-info-value">{{ $stats['total_services'] }}</p>
                        </div>
                    @endif
                    @if(isset($stats['average_rating']) && $stats['average_rating'])
                        <div class="col-6">
                            <p class="account-info-label">Gemiddelde Beoordeling</p>
                            <p class="account-info-value">
                                {{ number_format($stats['average_rating'], 1) }}/5 
                                <small style="color: var(--text-secondary-light);">({{ $stats['total_reviews'] ?? 0 }} reviews)</small>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabs = document.querySelectorAll('.dashboard-tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update active tab
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide content
            tabContents.forEach(content => {
                content.style.display = 'none';
            });
            
            // Show the corresponding tab content
            const targetContent = document.getElementById(targetTab + '-jobs');
            if (targetContent) {
                targetContent.style.display = 'block';
            }
        });
    });
});

// Initiate Chat function
function initiateChat() {
    // You can customize this to open your chat system
    // For now, it will show an alert or you can integrate with your chat widget
    alert('Chatfunctie zal binnenkort beschikbaar zijn. Neem contact met ons op via +32 490 45 80 09');
    // If you have a chat widget, uncomment and use:
    // if (typeof window.openChatWidget === 'function') {
    //     window.openChatWidget();
    // }
}
</script>
@endpush
