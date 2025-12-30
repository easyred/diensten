 
<section>
    <div class="alert alert-warning">
        <h5 class="alert-heading">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ __('Verwijder Account') }}
        </h5>
        <p class="mb-0">
            {{ __('Zodra uw account is verwijderd, worden al zijn bronnen en gegevens permanent verwijderd. Voordat u uw account verwijdert, download alle gegevens of informatie die u wilt behouden.') }}
        </p>
    </div>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
        <i class="fas fa-trash-alt me-2"></i>
        {{ __('Verwijder Account') }}
    </button>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        {{ __('Verwijder Account') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <h6 class="alert-heading">{{ __('Weet je zeker dat je je account wilt verwijderen?') }}</h6>
                            <p class="mb-0">
                                {{ __('Zodra uw account is verwijderd, worden al zijn bronnen en gegevens permanent verwijderd. Voer uw wachtwoord in om te bevestigen dat u uw account permanent wilt verwijderen.') }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Wachtwoord') }}</label>
                            <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Voer uw wachtwoord in') }}" required>
                            @error('userDeletion.password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Annuleer') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-2"></i>
                            {{ __('Verwijder Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

