@extends('layouts.dashboard-top-header')

@section('title', 'Profiel')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Profielinformatie -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Profielinformatie
                </h4>
                <p class="text-muted mb-0">Werk de profielinformatie en het e-mailadres van uw account bij.</p>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Wachtwoord Bijwerken -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-lock me-2"></i>
                    Wachtwoord Bijwerken
                </h4>
                <p class="text-muted mb-0">Zorg ervoor dat uw account een lang, willekeurig wachtwoord gebruikt om veilig te blijven.</p>
            </div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Account Verwijderen -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-trash-alt me-2"></i>
                    Account Verwijderen
                </h4>
                <p class="text-muted mb-0">Zodra uw account is verwijderd, worden al zijn bronnen en gegevens permanent verwijderd.</p>
            </div>
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection

