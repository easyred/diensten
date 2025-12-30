 
@extends('layouts.dashboard-top-header')

@section('title', 'Dekking gebieden')

@push('styles')
<style>
    /* Professionele Dekking Pagina Stijling */
    .coverage-card {
        background: var(--card-bg-light);
        border-radius: 16px;
        border: 1px solid var(--border-light);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
    }

    body.dark .coverage-card {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .coverage-card-header {
        padding: 1.75rem 2rem;
        border-bottom: 1px solid var(--border-light);
        background: transparent;
    }

    body.dark .coverage-card-header {
        border-bottom-color: var(--border-dark);
    }

    .coverage-card-title {
        font-size: 1.375rem;
        font-weight: 700;
        color: var(--text-primary-light);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .coverage-card-title i {
        color: #6b7280;
        font-size: 1.25rem;
    }

    body.dark .coverage-card-title {
        color: var(--text-primary-dark);
    }

    body.dark .coverage-card-title i {
        color: #9ca3af;
    }

    .coverage-card-body {
        padding: 2rem;
    }

    /* Geregistreerde Stad Weergave - Professioneel */
    .registered-city-display {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .registered-city-display i {
        color: #6b7280;
        font-size: 1.125rem;
    }

    .registered-city-label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        margin: 0;
    }

    .registered-city-value {
        color: var(--text-primary-light);
        font-size: 0.9375rem;
        font-weight: 600;
        margin: 0;
    }

    body.dark .registered-city-display {
        background: #1f2937;
        border-color: #374151;
    }

    body.dark .registered-city-display i {
        color: #9ca3af;
    }

    body.dark .registered-city-label {
        color: #9ca3af;
    }

    body.dark .registered-city-value {
        color: var(--text-primary-dark);
    }

    /* Professionele Knoppen */
    .coverage-btn {
        background: linear-gradient(135deg, var(--green-accent) 0%, #059669 100%);
        color: white;
        border: none;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: background 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
    }

    .coverage-btn:hover {
        background: linear-gradient(135deg, #059669 0%, var(--green-accent) 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        color: white;
    }

    .coverage-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .coverage-btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.8125rem;
    }

    /* Gemeentelijke Items */
    .municipality-item {
        background: var(--card-bg-light);
        border: 1px solid var(--border-light) !important;
        margin-bottom: 0.5rem;
        transition: background-color 0.2s ease;
    }

    .municipality-item:hover {
        background: #f9fafb;
    }

    body.dark .municipality-item {
        background: var(--card-bg-dark);
        border-color: var(--border-dark) !important;
    }

    body.dark .municipality-item:hover {
        background: #374151;
    }

    /* Dichtbijgelegen Boom Container */
    #nearby-tree {
        background: var(--card-bg-light);
        border: 1px solid var(--border-light) !important;
        border-radius: 12px;
        padding: 1rem;
    }

    body.dark #nearby-tree {
        background: var(--card-bg-dark);
        border-color: var(--border-dark) !important;
    }

    /* Lijst Groep Items */
    .list-group-item {
        background: var(--card-bg-light);
        border-color: var(--border-light);
        padding: 1rem 1.5rem;
        color: var(--text-primary-light);
    }

    body.dark .list-group-item {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        color: var(--text-primary-dark);
    }

    /* Dark mode for bg-light class used in JavaScript */
    body.dark .bg-light {
        background-color: #374151 !important;
        color: var(--text-primary-dark) !important;
    }

    /* Dark mode for text-muted */
    body.dark .text-muted {
        color: #9ca3af !important;
    }

    /* Dark mode for border elements */
    body.dark .border {
        border-color: var(--border-dark) !important;
    }

    body.dark .border-start {
        border-left-color: var(--border-dark) !important;
    }

    /* Custom class for covered items in dark mode */
    .covered-item {
        background: #f9fafb;
    }

    body.dark .covered-item {
        background: #374151 !important;
        color: var(--text-primary-dark) !important;
    }

    /* Formuliere Controls */
    .form-select {
        border: 1px solid var(--border-light);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        background: var(--card-bg-light);
        color: var(--text-primary-light);
    }

    body.dark .form-select {
        background: var(--card-bg-dark) !important;
        border-color: var(--border-dark) !important;
        color: var(--text-primary-dark) !important;
    }

    /* Form check inputs */
    body.dark .form-check-input {
        background-color: var(--card-bg-dark) !important;
        border-color: var(--border-dark) !important;
    }

    body.dark .form-check-input:checked {
        background-color: var(--green-accent) !important;
        border-color: var(--green-accent) !important;
    }

    body.dark .form-check-input:disabled {
        background-color: #4b5563 !important;
        border-color: #4b5563 !important;
        opacity: 0.5;
    }

    /* Labels */
    body.dark label {
        color: var(--text-primary-dark) !important;
    }

    body.dark .fw-medium {
        color: var(--text-primary-dark) !important;
    }

    body.dark .small {
        color: var(--text-secondary-dark) !important;
    }

    /* Form labels */
    body.dark .form-label {
        color: var(--text-primary-dark) !important;
    }

    /* Text colors */
    body.dark p {
        color: var(--text-primary-dark) !important;
    }

    body.dark h6 {
        color: var(--text-primary-dark) !important;
    }

    /* Delete button */
    .delete-btn {
        background: #ef4444 !important;
        color: white !important;
    }

    body.dark .delete-btn {
        background: #dc2626 !important;
        color: white !important;
    }

    body.dark .delete-btn:hover {
        background: #b91c1c !important;
    }

    /* Verwijder alert-info styling */
    .alert-info {
        background: transparent;
        border: none;
        padding: 0;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Dichtbij gelegen steden sectie -->
        <div class="coverage-card">
            <div class="coverage-card-header d-flex justify-content-between align-items-center">
                <h5 class="coverage-card-title">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Dichtbijgelegen Steden</span>
                </h5>
                <a href="{{ route('service-provider.dashboard') }}" class="btn-accept" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                    <i class="fas fa-arrow-left me-2"></i>
                    Terug
                </a>
            </div>
            <div class="coverage-card-body">
                <p class="text-muted mb-3">Breid gemeenten uit om dichtbijgelegen steden binnen de geselecteerde straal te zien.</p>

                <div class="registered-city-display">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <p class="registered-city-label mb-0">Uw geregistreerde stad</p>
                        <p class="registered-city-value mb-0">{{ Auth::user()->city }}</p>
                    </div>
                </div>
                
                <div class="row align-items-center mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <label class="form-label me-2 mb-0">Straal:</label>
                            <select id="radius-selector" class="form-select form-select-sm" style="width: auto;">
                                <option value="10">10 km</option>
                                <option value="20" selected>20 km</option>
                                <option value="30">30 km</option>
                                <option value="50">50 km</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button id="refresh-nearby" class="coverage-btn coverage-btn-sm">
                            <i class="fas fa-sync-alt me-1"></i>
                            Vernieuwen
                        </button>
                    </div>
                </div>

                <div id="nearby-section">
                    <h6 class="mb-2">Dichtbijgelegen Gemeenten</h6>
                    <p class="text-muted small mb-3">
                        <span id="nearby-description">Klik om gemeenten uit te breiden en dichtbijgelegen steden te selecteren:</span>
                        <span id="auto-load-message" class="d-none text-primary fw-medium">Dichtbijgelegen gemeenten worden getoond op basis van uw geregistreerde stad!</span>
                    </p>
                    
                    <div id="nearby-tree" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-muted text-center py-4">Dichtbijgelegen steden laden...</div>
                    </div>
                    
                    <div class="mt-3 d-flex gap-2">
                        <button id="select-all-nearby" class="coverage-btn coverage-btn-sm">
                            Selecteer Alles
                        </button>
                        <button id="add-selected-nearby" class="coverage-btn coverage-btn-sm">
                        Selectie toevoegen en opslaan                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Huidige dekkingen -->
        <div class="coverage-card">
            <div class="coverage-card-header">
                <h5 class="coverage-card-title">
                    <i class="fas fa-list"></i>
                    <span>Uw Gemeenten</span>
                </h5>
            </div>
            <div class="coverage-card-body">
                @if ($coverages->isEmpty())
                    <p class="text-muted">U heeft nog geen gemeenten toegevoegd.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($coverages as $cov)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    @if ($cov->coverage_type === 'municipality')
                                        <div class="fw-medium">{{ $cov->hoofdgemeente }}</div>
                                        <div class="text-muted small">
                                            {{ $counts[$cov->hoofdgemeente] ?? 0 }} steden gedekt (hele gemeente)
                                        </div>
                                    @else
                                        <div class="fw-medium">{{ $cov->hoofdgemeente }} - {{ $cov->city }}</div>
                                        <div class="text-muted small">
                                            1 stad gedekt (alleen specifieke stad)
                                        </div>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('service-provider.coverage.destroy', $cov->id) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm delete-btn" style="background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 500; transition: background 0.2s ease;">
                                        Verwijderen
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>

const nearbySection = document.getElementById('nearby-section');
const nearbyTree = document.getElementById('nearby-tree');
const selectAllBtn = document.getElementById('select-all-nearby');
const addSelectedBtn = document.getElementById('add-selected-nearby');
const radiusSelector = document.getElementById('radius-selector');
const refreshBtn = document.getElementById('refresh-nearby');

let selectedNearbyMunicipalities = new Set();
let selectedCities = new Set(); // Volg geselecteerde steden



// Selecteer alles functionaliteit
selectAllBtn.addEventListener('click', async () => {
    const checkboxes = nearbyTree.querySelectorAll('input[type="checkbox"]:not(:disabled)');
    
    for (const checkbox of checkboxes) {
        checkbox.checked = true;
        const municipality = checkbox.dataset.municipality;
        const type = checkbox.dataset.type;
        
        if (type === 'municipality') {
            selectedNearbyMunicipalities.add(municipality);
            
            // Laad steden voor deze gemeente als ze nog niet zijn geladen
            const citiesContainer = checkbox.closest('.municipality-item').querySelector('.cities-container');
            if (citiesContainer && citiesContainer.classList.contains('d-none')) {
                // Steden zijn nog niet geladen, laad ze eerst
                citiesContainer.classList.remove('d-none');
                const expandIcon = checkbox.closest('.municipality-item').querySelector('.expand-area i');
                expandIcon.className = 'fas fa-minus expand-icon text-muted';
                await loadCitiesForMunicipality(municipality, citiesContainer);
            }
            
            // Selecteer ook alle steden in deze gemeente
            if (citiesContainer) {
                citiesContainer.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(cityCheckbox => {
                    cityCheckbox.checked = true;
                    selectedCities.add(cityCheckbox.dataset.municipality);
                });
            }
        } else {
            selectedCities.add(municipality);
        }
    }
    updateAddButton();
});

// Voeg geselecteerde gemeenten toe
addSelectedBtn.addEventListener('click', async () => {
    if (selectedNearbyMunicipalities.size === 0 && selectedCities.size === 0) {
        alert('Selecteer alstublieft ten minste één gemeente of stad.');
        return;
    }

    try {
        addSelectedBtn.disabled = true;
        addSelectedBtn.textContent = 'Toevoegen...';

        const response = await fetch('{{ route("service-provider.coverage.bulk") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                municipalities: Array.from(selectedNearbyMunicipalities),
                cities: Array.from(selectedCities)
            })
        });

        let result;
        try {
            result = await response.json();
        } catch (e) {
            console.error('Failed to parse response:', e);
            throw new Error('Invalid response from server');
        }

        if (!response.ok) {
            throw new Error(result.message || `Server error: ${response.status}`);
        }

        if (result.success) {
            // Toon succesbericht
            const successDiv = document.createElement('div');
            successDiv.className = 'alert alert-success alert-dismissible fade show';
            successDiv.innerHTML = `
                ${result.message || 'Coverage areas added successfully.'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Invoegen bovenaan de huidige dekkingen sectie
            const coveragesSection = document.querySelector('.card:last-child .card-body');
            if (coveragesSection) {
                coveragesSection.insertBefore(successDiv, coveragesSection.firstChild);
            }

            // Verwijder succesbericht na 5 seconden
            setTimeout(() => {
                successDiv.remove();
            }, 5000);

            // Wis selecties
            selectedNearbyMunicipalities.clear();
            selectedCities.clear();
            nearbyTree.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateAddButton();

            // Herlaad de pagina om bijgewerkte dekkingen te tonen
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert('Fout: ' + (result.message || 'Kon gemeenten niet toevoegen'));
        }
    } catch (error) {
        console.error('Fout bij het toevoegen van gemeenten:', error);
        alert('Fout bij het toevoegen van gemeenten. Probeer het opnieuw.');
    } finally {
        addSelectedBtn.disabled = false;
        addSelectedBtn.textContent = 'Selectie toevoegen en opslaan';
    }
});

function updateAddButton() {
    const totalSelected = selectedNearbyMunicipalities.size + selectedCities.size;
    addSelectedBtn.textContent = `Selectie toevoegen en opslaan (${totalSelected})`;
    addSelectedBtn.disabled = totalSelected === 0;
}



// Auto-vul dichtbijgelegen steden wanneer pagina laadt op basis van geregistreerde stad van gebruiker
document.addEventListener('DOMContentLoaded', async () => {
    // Verkrijg geregistreerde stad van gebruiker
    const userCity = '{{ Auth::user()->city }}';
    
    if (userCity) {
        // Toon automatisch laadbericht
        document.getElementById('nearby-description').classList.add('d-none');
        document.getElementById('auto-load-message').classList.remove('d-none');
        
        // Laad dichtbijgelegen gemeenten op basis van geregistreerde stad van gebruiker
        await loadNearbyMunicipalitiesFromCity(userCity, parseInt(radiusSelector.value));
    } else {
        // Geen stad geregistreerd, toon lege staat
        nearbyTree.innerHTML = '<div class="text-muted text-center py-4">Geen stad geregistreerd. Werk alstublieft eerst uw profiel bij.</div>';
    }
});

// Straal selecteer event
radiusSelector.addEventListener('change', async () => {
    const userCity = '{{ Auth::user()->city }}';
    const selectedRadius = parseInt(radiusSelector.value);
    
    if (userCity) {
        nearbyTree.innerHTML = '<div class="text-muted text-center py-4">Dichtbijgelegen steden laden...</div>';
        await loadNearbyMunicipalitiesFromCity(userCity, selectedRadius);
    }
});

// Vernieuw knop klik event
refreshBtn.addEventListener('click', async () => {
    const userCity = '{{ Auth::user()->city }}';
    const selectedRadius = parseInt(radiusSelector.value);
    
    if (userCity) {
        nearbyTree.innerHTML = '<div class="text-muted text-center py-4">Dichtbijgelegen steden laden...</div>';
        await loadNearbyMunicipalitiesFromCity(userCity, selectedRadius);
    }
});

// Functie om dichtbijgelegen gemeenten te laden vanuit geregistreerde stad van gebruiker
async function loadNearbyMunicipalitiesFromCity(city, radius = 20) {
    try {
        // Get user's postal code for fallback
        const userPostalCode = '{{ Auth::user()->postal_code ?? "" }}';
        
        // Zoek eerst de gemeente (Hoofdgemeente) voor de geregistreerde stad van gebruiker
        let searchUrl = `{{ route('service-provider.municipalities.search') }}?term=${encodeURIComponent(city)}`;
        if (userPostalCode) {
            searchUrl += `&postal_code=${encodeURIComponent(userPostalCode)}`;
        }
        
        const municipalityRes = await fetch(searchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!municipalityRes.ok) {
            throw new Error(`Kon gemeenten niet zoeken: ${municipalityRes.status}`);
        }
        
        const municipalities = await municipalityRes.json();
        
        if (municipalities.length === 0) {
            nearbyTree.innerHTML = '<div class="text-muted text-center py-4">Kon gemeente niet vinden voor uw stad: ' + city + '</div>';
            return;
        }
        
        // Gebruik de eerste overeenkomende gemeente als basis
        const baseMunicipality = municipalities[0];
        
        // Zoek nu nabijgelegen gemeenten vanuit deze basis
        const nearbyRes = await fetch(`{{ route('service-provider.municipalities.nearby') }}?municipality=${encodeURIComponent(baseMunicipality)}&radius=${radius}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!nearbyRes.ok) {
            throw new Error(`Kon nabijgelegen gemeenten niet ophalen: ${nearbyRes.status}`);
        }
        
        const nearbyData = await nearbyRes.json();
        
        if (nearbyData.length === 0) {
            nearbyTree.innerHTML = '<div class="text-muted text-center py-4">Geen nabijgelegen gemeenten gevonden binnen ' + radius + 'km van ' + city + '</div>';
            return;
        }
        
        // Toon hiërarchische boom
        const mappedData = nearbyData.map(item => ({
            municipality: item.Hoofdgemeente,
            distance: item.distance,
            sources: [baseMunicipality]
        }));
        
        displayNearbyTree(mappedData);
        
    } catch (error) {
        console.error('Fout bij het laden van nabijgelegen gemeenten:', error);
        nearbyTree.innerHTML = '<div class="text-danger text-center py-4">Fout: ' + error.message + '</div>';
    }
}

// Functie om nabijgelegen steden voor alle gemeenten te laden
async function loadAllNearbyCities(municipalities, radius = 20) {
    try {
        const municipalityData = new Map(); // Groeperen per gemeente
        
        // Laad nabijgelegen steden voor elke gemeente
        for (const municipality of municipalities) {
            try {
                const res = await fetch(`{{ route('service-provider.municipalities.nearby') }}?municipality=${encodeURIComponent(municipality)}&radius=${radius}`);
                const data = await res.json();
                
                // Groepeer nabijgelegen steden per gemeente
                data.forEach(item => {
                    if (!municipalityData.has(item.Hoofdgemeente)) {
                        municipalityData.set(item.Hoofdgemeente, {
                            municipality: item.Hoofdgemeente,
                            distance: item.distance,
                            sources: [municipality],
                            cities: []
                        });
                    } else {
                        // Als het al bestaat, voeg deze gemeente als een andere bron toe
                        municipalityData.get(item.Hoofdgemeente).sources.push(municipality);
                    }
                });
            } catch (error) {
                console.error(`Fout bij het laden van nabijgelegen steden voor ${municipality}:`, error);
            }
        }
        
        // Toon hiërarchische boom
        displayNearbyTree(Array.from(municipalityData.values()));
        
    } catch (error) {
        console.error('Fout bij het laden van nabijgelegen steden:', error);
        nearbyTree.innerHTML = '<div class="text-danger text-center py-4">Fout bij het laden van nabijgelegen steden</div>';
    }
}

// Functie om nabijgelegen steden in boomstructuur weer te geven
function displayNearbyTree(municipalityData) {
    if (municipalityData.length === 0) {
        nearbyTree.innerHTML = '<div class="text-muted text-center py-4">Geen nabijgelegen gemeenten gevonden binnen geselecteerde straal</div>';
        return;
    }
    
    // Verkrijg lijst van reeds gedekte gemeenten
    const coverageItems = document.querySelectorAll('.list-group-item');
    const existingMunicipalities = [];
    coverageItems.forEach(item => {
        const municipalityName = item.querySelector('.fw-medium').textContent;
        existingMunicipalities.push(municipalityName);
    });
    
    selectedNearbyMunicipalities.clear();
    selectedCities.clear();
    
    // Sorteer op afstand
    municipalityData.sort((a, b) => a.distance - b.distance);
    
    nearbyTree.innerHTML = municipalityData.map((item, index) => {
        const isAlreadyCovered = existingMunicipalities.includes(item.municipality);
        const checkboxDisabled = isAlreadyCovered ? 'disabled' : '';
        const checkboxClass = isAlreadyCovered ? 'form-check-input text-muted' : 'form-check-input text-primary';
        const itemClass = isAlreadyCovered ? 'covered-item' : '';
        const statusText = isAlreadyCovered ? ' (Al gedekt)' : '';
        
        // Speciale stijl voor de eigen gemeente van de gebruiker (afstand = 0)
        const isUserMunicipality = item.distance === 0;
        const userMunicipalityClass = '';
        const userMunicipalityBg = '';
        const userMunicipalityIcon = 'fas fa-plus expand-icon text-muted';
        const userMunicipalityText = isUserMunicipality ? 'Uw Gemeente' : (item.sources.length > 1 ? `Dichtbij ${item.sources.join(', ')}` : `Dichtbij ${item.sources[0]}`);
        const distanceText = isUserMunicipality ? 'Uw Locatie' : `${item.distance ? item.distance.toFixed(1) : '0.0'}km`;
        
        return `
            <div class="municipality-item border rounded ${itemClass} ${userMunicipalityClass} ${userMunicipalityBg}">
                <div class="d-flex align-items-center p-2">
                    <div class="expand-area d-flex align-items-center me-2" data-municipality="${item.municipality}" style="cursor: pointer; padding: 4px;">
                        <i class="${userMunicipalityIcon}" style="font-size: 0.8rem; transition: transform 0.2s;"></i>
                    </div>
                    <input type="checkbox" id="municipality-${item.municipality}" 
                           class="${checkboxClass} me-2"
                           data-municipality="${item.municipality}"
                           data-type="municipality"
                           ${checkboxDisabled}>
                    <label for="municipality-${item.municipality}" class="flex-fill small" style="cursor: pointer;">
                        <div class="fw-medium">
                            ${isUserMunicipality ? '<i class="fas fa-star text-warning me-1"></i>' : ''}
                            ${item.municipality} (${distanceText})${statusText}
                        </div>
                        <div class="text-muted small">${userMunicipalityText}</div>
                    </label>
                </div>
                <div class="cities-container d-none ps-5 pe-2 pb-2">
                    <div class="text-muted small py-2"><i class="fas fa-spinner fa-spin me-1"></i>Steden laden...</div>
                </div>
            </div>
        `;
    }).join('');
    
    // Gebruik eventdelegatie voor klikken op uitbreidingsgebied
    nearbyTree.addEventListener('click', async (e) => {
        const expandArea = e.target.closest('.expand-area');
        if (!expandArea) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Uitbreidingsgebied aangeklikt'); // Debug log
        
        const municipality = expandArea.dataset.municipality;
        const municipalityItem = expandArea.closest('.municipality-item');
        const citiesContainer = municipalityItem.querySelector('.cities-container');
        const expandIcon = expandArea.querySelector('i');
        
        console.log('Gemeente:', municipality); // Debug log
        console.log('Stedencontainer:', citiesContainer); // Debug log
        
        if (citiesContainer && citiesContainer.classList.contains('d-none')) {
            // Uitbreiden
            console.log('Uitbreiden...'); // Debug log
            citiesContainer.classList.remove('d-none');
            
            // Verander naar min icoon
            expandIcon.className = 'fas fa-minus expand-icon text-muted';
            expandIcon.style.transform = 'rotate(0deg)';
            
            // Laad steden voor deze gemeente
            await loadCitiesForMunicipality(municipality, citiesContainer);
        } else if (citiesContainer) {
            // Inklappen
            console.log('Inklappen...'); // Debug log
            citiesContainer.classList.add('d-none');
            
            // Verander terug naar plus icoon
            expandIcon.className = 'fas fa-plus expand-icon text-muted';
            expandIcon.style.transform = 'rotate(0deg)';
        }
    });
    
    // Verbind checkbox-events
    nearbyTree.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', async () => {
            const municipality = checkbox.dataset.municipality;
            const type = checkbox.dataset.type;
            
            if (checkbox.checked && !checkbox.disabled) {
                if (type === 'municipality') {
                    // Selecteer gemeente en al zijn steden
                    selectedNearbyMunicipalities.add(municipality);
                    
                    // Laad steden voor deze gemeente als ze nog niet zijn geladen
                    const citiesContainer = checkbox.closest('.municipality-item').querySelector('.cities-container');
                    if (citiesContainer && citiesContainer.classList.contains('d-none')) {
                        // Steden zijn nog niet geladen, laad ze eerst
                        citiesContainer.classList.remove('d-none');
                        const expandIcon = checkbox.closest('.municipality-item').querySelector('.expand-area i');
                        expandIcon.className = 'fas fa-minus expand-icon text-muted';
                        await loadCitiesForMunicipality(municipality, citiesContainer);
                    }
                    
                    // Selecteer nu alle steden in deze gemeente
                    if (citiesContainer) {
                        citiesContainer.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(cityCheckbox => {
                            cityCheckbox.checked = true;
                            selectedCities.add(cityCheckbox.dataset.municipality);
                        });
                    }
                } else {
                    selectedCities.add(municipality);
                }
            } else {
                if (type === 'municipality') {
                    // Deselecteer gemeente en al zijn steden
                    selectedNearbyMunicipalities.delete(municipality);
                    const citiesContainer = checkbox.closest('.municipality-item').querySelector('.cities-container');
                    if (citiesContainer) {
                        citiesContainer.querySelectorAll('input[type="checkbox"]').forEach(cityCheckbox => {
                            cityCheckbox.checked = false;
                            selectedCities.delete(cityCheckbox.dataset.municipality);
                        });
                    }
                } else {
                    selectedCities.delete(municipality);
                }
            }
            updateAddButton();
        });
    });
    
    updateAddButton();
}

// Functie om steden voor een specifieke gemeente te laden
async function loadCitiesForMunicipality(municipality, container) {
    try {
        const res = await fetch(`{{ url('/service-provider/municipalities') }}/${encodeURIComponent(municipality)}/towns`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) {
            throw new Error(`Kon steden niet laden: ${res.status}`);
        }
        
        const cities = await res.json();
        
        if (cities.length === 0) {
            container.innerHTML = '<div class="text-muted small py-2">Geen steden gevonden</div>';
            return;
        }
        
        // Verkrijg geregistreerde stad van gebruiker voor afstandsberekening
        const userCity = '{{ Auth::user()->city }}';
        
        // Verkrijg lijst van reeds gedekte gemeenten
        const coverageItems = document.querySelectorAll('.list-group-item');
        const existingMunicipalities = [];
        coverageItems.forEach(item => {
            const municipalityName = item.querySelector('.fw-medium').textContent;
            existingMunicipalities.push(municipalityName);
        });
        
        // Steden bevatten al afstandgegevens van de server
        const citiesWithDistances = cities;
        console.log('Steden met afstanden:', citiesWithDistances); // Debug log
        
        container.innerHTML = citiesWithDistances.map(city => {
            const isAlreadyCovered = existingMunicipalities.includes(city.Plaatsnaam_NL);
            const checkboxDisabled = isAlreadyCovered ? 'disabled' : '';
            const checkboxClass = isAlreadyCovered ? 'form-check-input text-muted' : 'form-check-input text-primary';
            const itemClass = isAlreadyCovered ? 'covered-item' : '';
            const statusText = isAlreadyCovered ? ' (Al gedekt)' : '';
            
            // Toon afstand voor alle steden (0km voor de eigen stad van gebruiker)
            console.log('Bezig met verwerking van stad:', city.Plaatsnaam_NL, 'Afstand:', city.distance, 'Type:', typeof city.distance); // Debug log
            const distanceText = city.distance !== undefined && city.distance !== null ? ` (${city.distance.toFixed(1)}km)` : '';
            
            return `
                <div class="d-flex align-items-center p-2 border-start border-2 ${itemClass}">
                    <input type="checkbox" id="city-${city.Plaatsnaam_NL}" 
                           class="${checkboxClass} me-2"
                           data-municipality="${city.Plaatsnaam_NL}"
                           data-type="city"
                           ${checkboxDisabled}>
                    <label for="city-${city.Plaatsnaam_NL}" class="flex-fill small" style="cursor: pointer;">
                        <div class="fw-medium">➖ ${city.Plaatsnaam_NL} (${city.Postcode})${distanceText}${statusText}</div>
                    </label>
                </div>
            `;
        }).join('');
        
        // Verbind steden checkbox events
        container.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const cityMunicipality = checkbox.dataset.municipality;
                
                if (checkbox.checked && !checkbox.disabled) {
                    selectedCities.add(cityMunicipality);
                } else {
                    selectedCities.delete(cityMunicipality);
                }
                updateAddButton();
            });
        });
        
    } catch (error) {
        console.error('Fout bij het laden van steden:', error);
        container.innerHTML = '<div class="text-danger small py-2">Fout: ' + error.message + '</div>';
    }
}
</script>
@endsection
 