 
@extends('layouts.dashboard-top-header')

@section('title', 'Schema Beheer')

@section('page-title', 'Schema Beheer')

@section('sidebar-nav')
    <x-service-provider-sidebar />
@endsection

@push('styles')
<style>
    /* Schema Pagina Styling - Gelijkend aan Andere Loodgieter Pagina's */
    .schedule-card {
        background: var(--card-bg-light);
        border-radius: 16px;
        border: 1px solid var(--border-light);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
    }

    body.dark .schedule-card {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    .schedule-card-header {
        padding: 1.75rem 2rem;
        border-bottom: 1px solid var(--border-light);
        background: transparent;
    }

    body.dark .schedule-card-header {
        border-bottom-color: var(--border-dark);
    }

    .schedule-card-title {
        font-size: 1.375rem;
        font-weight: 700;
        color: var(--text-primary-light);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .schedule-card-title i {
        color: #6b7280;
        font-size: 1.25rem;
    }

    body.dark .schedule-card-title {
        color: var(--text-primary-dark);
    }

    body.dark .schedule-card-title i {
        color: #9ca3af;
    }

    .schedule-card-body {
        padding: 2rem;
    }

    /* Dag Schema Item */
    .day-schedule-item {
        margin-bottom: 1.25rem;
        padding: 1.25rem;
        border: 1px solid var(--border-light);
        border-radius: 12px;
        background: var(--card-bg-light);
        transition: all 0.2s ease;
    }

    .day-schedule-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    body.dark .day-schedule-item {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
    }

    body.dark .day-schedule-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }

    .day-label {
        font-weight: 600;
        color: var(--text-primary-light);
        margin-bottom: 1rem;
        font-size: 1rem;
    }

    body.dark .day-label {
        color: var(--text-primary-dark);
    }

    /* Modus Knoppen */
    .mode-btn {
        padding: 0.5rem 1rem;
        border: 1px solid var(--border-light);
        background: var(--card-bg-light);
        color: var(--text-primary-light);
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .mode-btn:hover {
        background: #f9fafb;
        border-color: var(--green-accent);
        color: var(--green-accent);
    }

    .mode-btn.active {
        background: linear-gradient(135deg, var(--green-accent) 0%, #059669 100%);
        color: white;
        border-color: var(--green-accent);
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
    }

    body.dark .mode-btn {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        color: var(--text-primary-dark);
    }

    body.dark .mode-btn:hover {
        background: #374151;
        border-color: var(--green-accent);
        color: var(--green-accent);
    }

    body.dark .mode-btn.active {
        background: linear-gradient(135deg, var(--green-accent) 0%, #059669 100%);
        color: white;
    }

    /* Status Badge */
    .day-status-badge {
        font-size: 0.875rem;
        font-weight: 600;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        background: rgba(16, 185, 129, 0.1);
        color: var(--green-accent);
    }

    body.dark .day-status-badge {
        background: rgba(16, 185, 129, 0.2);
        color: var(--green-accent);
    }

    /* Tijd Invoervelden */
    .time-input-group {
        display: grid;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .time-input-group.split {
        grid-template-columns: repeat(4, 1fr);
    }

    .time-input-group.full {
        grid-template-columns: repeat(2, 1fr);
    }

    .time-input-label {
        display: block;
        font-size: 0.8125rem;
        color: var(--text-secondary-light);
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    body.dark .time-input-label {
        color: var(--text-secondary-dark);
    }

    .time-input {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        font-size: 0.875rem;
        background: var(--card-bg-light);
        color: var(--text-primary-light);
        transition: all 0.2s ease;
    }

    .time-input:focus {
        outline: none;
        border-color: var(--green-accent);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    body.dark .time-input {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        color: var(--text-primary-dark);
    }

    body.dark .time-input:focus {
        border-color: var(--green-accent);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
    }

    /* Sidebar Items */
    .holiday-item,
    .vacation-item {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        margin-bottom: 0.75rem;
        padding: 0.875rem;
        background: var(--card-bg-light);
        border: 1px solid var(--border-light);
        border-radius: 8px;
    }

    body.dark .holiday-item,
    body.dark .vacation-item {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
    }

    .holiday-input,
    .vacation-input {
        flex: 1;
        padding: 0.625rem 0.75rem;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        font-size: 0.875rem;
        background: var(--card-bg-light);
        color: var(--text-primary-light);
    }

    body.dark .holiday-input,
    body.dark .vacation-input {
        background: var(--card-bg-dark);
        border-color: var(--border-dark);
        color: var(--text-primary-dark);
    }

    /* Knoppen */
    .btn-schedule {
        background: linear-gradient(135deg, var(--green-accent) 0%, #059669 100%);
        color: white;
        border: none;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
    }

    .btn-schedule:hover {
        background: linear-gradient(135deg, #059669 0%, var(--green-accent) 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        color: white;
    }

    .btn-schedule:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-remove {
        background: #dc3545;
        color: white;
        border: none;
        padding: 0.625rem 1rem;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.8125rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-remove:hover {
        background: #c82333;
    }

    .btn-save {
        background: linear-gradient(135deg, var(--green-accent) 0%, #059669 100%);
        color: white;
        border: none;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #059669 0%, var(--green-accent) 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    }

    /* Berichtencontainer */
    #message-container {
        margin-bottom: 1.5rem;
    }

    .message {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        text-align: center;
        font-weight: 500;
    }

    .message.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .message.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    body.dark .message.success {
        background: rgba(16, 185, 129, 0.2);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.3);
    }

    body.dark .message.error {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.3);
    }

    /* Laatste Geüpdatete Tekst */
    .last-updated {
        margin-top: 1rem;
        color: var(--text-secondary-light);
        font-size: 0.875rem;
    }

    body.dark .last-updated {
        color: var(--text-secondary-dark);
    }

    /* Sectietitel */
    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary-light);
        margin: 0 0 1rem 0;
    }

    body.dark .section-title {
        color: var(--text-primary-dark);
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header Kaart -->
        <div class="schedule-card">
            <div class="schedule-card-header">
                <h5 class="schedule-card-title">
                    <i class="fas fa-clock"></i>
                    <span>Wekelijks Schema Beheer</span>
                </h5>
            </div>
            <div class="schedule-card-body">
                <p style="color: var(--text-secondary-light); margin: 0;">
                    Stel uw werktijden, feestdagen en vakanties in
                </p>
            </div>
        </div>

        <!-- Berichtencontainer -->
        <div id="message-container"></div>

        <!-- Hoofdinhoud -->
        <div class="row">
            <!-- Werkuren Sectie -->
            <div class="col-lg-8 mb-4">
                <div class="schedule-card">
                    <div class="schedule-card-header">
                        <h5 class="schedule-card-title">
                            <i class="fas fa-calendar-week"></i>
                            <span>Werktijden</span>
                        </h5>
                    </div>
                    <div class="schedule-card-body">
                        <form id="scheduleForm">
                            @csrf
                            
                            @php
                                $days = ['monday' => 'Maandag', 'tuesday' => 'Dinsdag', 'wednesday' => 'Woensdag', 
                                        'thursday' => 'Donderdag', 'friday' => 'Vrijdag', 'saturday' => 'Zaterdag', 'sunday' => 'Zondag'];
                                $scheduleData = $schedule->schedule_data ?? [];
                            @endphp
                            
                            @foreach($days as $key => $label)
                                @php
                                    $day = $scheduleData[$key] ?? ['mode' => 'open24', 'split' => ['o1' => '09:00', 'c1' => '12:00', 'o2' => '13:30', 'c2' => '19:00'], 'full' => ['o' => '09:00', 'c' => '19:00']];
                                    $mode = $day['mode'] ?? 'open24';
                                    $split = $day['split'] ?? ['o1' => '09:00', 'c1' => '12:00', 'o2' => '13:30', 'c2' => '19:00'];
                                    $full = $day['full'] ?? ['o' => '09:00', 'c' => '19:00'];
                                @endphp
                                
                                <div class="day-schedule-item" data-day="{{ $key }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="day-label">{{ $label }}</div>
                                        @php
                                            $statusText = '';
                                            $statusColor = '';
                                            
                                            switch($mode) {
                                                case 'closed':
                                                    $statusText = 'Gesloten';
                                                    $statusColor = '#dc3545';
                                                    break;
                                                case 'open24':
                                                    $statusText = '24 Uur';
                                                    $statusColor = '#28a745';
                                                    break;
                                                case 'split':
                                                    $statusText = 'Gesplitste Uren';
                                                    $statusColor = 'var(--green-accent)';
                                                    break;
                                                case 'fullday':
                                                    $statusText = 'Overdag';
                                                    $statusColor = '#17a2b8';
                                                    break;
                                                default:
                                                    $statusText = 'Gesplitste Uren';
                                                    $statusColor = 'var(--green-accent)';
                                            }
                                        @endphp
                                        <span class="day-status-badge" data-day="{{ $key }}" style="color: {{ $statusColor }};">{{ $statusText }}</span>
                                    </div>
                                    
                                    <!-- Modus Knoppen -->
                                    <div class="d-flex gap-2 mb-3 flex-wrap">
                                        <button type="button" class="mode-btn {{ $mode === 'closed' ? 'active' : '' }}" data-mode="closed">Gesloten</button>
                                        <button type="button" class="mode-btn {{ $mode === 'open24' ? 'active' : '' }}" data-mode="open24">24 Uur</button>
                                        <button type="button" class="mode-btn {{ $mode === 'fullday' ? 'active' : '' }}" data-mode="fullday">Overdag</button>
                                        <button type="button" class="mode-btn {{ $mode === 'split' ? 'active' : '' }}" data-mode="split">Gesplitste Uren</button>
                                    </div>

                                    <input type="hidden" name="schedule_data[{{ $key }}][mode]" value="{{ $mode }}">
                                    
                                    <!-- Gesplitste Uren -->
                                    <div class="time-input-group split" style="display: {{ $mode === 'split' ? 'grid' : 'none' }};">
                                        <div>
                                            <label class="time-input-label">Open 1</label>
                                            <input type="time" name="schedule_data[{{ $key }}][split][o1]" value="{{ $split['o1'] }}" class="time-input">
                                        </div>
                                        <div>
                                            <label class="time-input-label">Sluiten 1</label>
                                            <input type="time" name="schedule_data[{{ $key }}][split][c1]" value="{{ $split['c1'] }}" class="time-input">
                                        </div>
                                        <div>
                                            <label class="time-input-label">Open 2</label>
                                            <input type="time" name="schedule_data[{{ $key }}][split][o2]" value="{{ $split['o2'] }}" class="time-input">
                                        </div>
                                        <div>
                                            <label class="time-input-label">Sluiten 2</label>
                                            <input type="time" name="schedule_data[{{ $key }}][split][c2]" value="{{ $split['c2'] }}" class="time-input">
                                        </div>
                                    </div>

                                    <!-- Hele Dag -->
                                    <div class="time-input-group full" style="display: {{ $mode === 'fullday' ? 'grid' : 'none' }};">
                                        <div>
                                            <label class="time-input-label">Open</label>
                                            <input type="time" name="schedule_data[{{ $key }}][full][o]" value="{{ $full['o'] }}" class="time-input">
                                        </div>
                                        <div>
                                            <label class="time-input-label">Sluiten</label>
                                            <input type="time" name="schedule_data[{{ $key }}][full][c]" value="{{ $full['c'] }}" class="time-input">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mb-4">
                <!-- Feestdagen -->
                <div class="schedule-card mb-4">
                    <div class="schedule-card-header">
                        <h5 class="schedule-card-title">
                            <i class="fas fa-calendar-times"></i>
                            <span>Feestdagen</span>
                        </h5>
                    </div>
                    <div class="schedule-card-body">
                        <div id="holidays-list">
                            @if(!empty($schedule->holidays))
                                @foreach($schedule->holidays as $holiday)
                                    <div class="holiday-item">
                                        <input type="date" name="holidays[]" value="{{ $holiday }}" class="holiday-input">
                                        <button type="button" onclick="removeHoliday(this)" class="btn-remove">Verwijderen</button>
                                    </div>
                                @endforeach
                            @else
                                <div class="holiday-item">
                                    <input type="date" name="holidays[]" value="" class="holiday-input">
                                    <button type="button" onclick="removeHoliday(this)" class="btn-remove">Verwijderen</button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addHoliday()" class="btn-schedule" style="width: 100%; margin-top: 0.75rem;">
                            <i class="fas fa-plus me-2"></i>
                            Feestdag Toevoegen
                        </button>
                    </div>
                </div>

                <!-- Vakanties -->
                <div class="schedule-card">
                    <div class="schedule-card-header">
                        <h5 class="schedule-card-title">
                            <i class="fas fa-plane"></i>
                            <span>Vakanties</span>
                        </h5>
                    </div>
                    <div class="schedule-card-body">
                        <div id="vacations-list">
                            @if(!empty($schedule->vacations))
                                @foreach($schedule->vacations as $vacation)
                                    <div class="vacation-item">
                                        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem;">
                                            <div style="display: flex; gap: 0.5rem;">
                                                <input type="date" name="vacations[{{ $loop->index }}][from]" value="{{ $vacation['from'] }}" placeholder="Van" class="vacation-input" style="flex: 1;">
                                                <input type="date" name="vacations[{{ $loop->index }}][to]" value="{{ $vacation['to'] }}" placeholder="Tot" class="vacation-input" style="flex: 1;">
                                            </div>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <input type="text" name="vacations[{{ $loop->index }}][note]" value="{{ $vacation['note'] ?? '' }}" placeholder="Opmerking" class="vacation-input">
                                                <button type="button" onclick="removeVacation(this)" class="btn-remove">Verwijderen</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="vacation-item">
                                    <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem;">
                                        <div style="display: flex; gap: 0.5rem;">
                                            <input type="date" name="vacations[0][from]" value="" placeholder="Van" class="vacation-input" style="flex: 1;">
                                            <input type="date" name="vacations[0][to]" value="" placeholder="Tot" class="vacation-input" style="flex: 1;">
                                        </div>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <input type="text" name="vacations[0][note]" value="" placeholder="Opmerking" class="vacation-input">
                                            <button type="button" onclick="removeVacation(this)" class="btn-remove">Verwijderen</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addVacation()" class="btn-schedule" style="width: 100%; margin-top: 0.75rem;">
                            <i class="fas fa-plus me-2"></i>
                            Vakantie Toevoegen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opslaan Sectie -->
        <div class="schedule-card">
            <div class="schedule-card-body text-center">
                <button type="button" onclick="saveSchedule()" class="btn-save">
                    <i class="fas fa-save me-2"></i>
                    Schema Opslaan
                </button>
                <p class="last-updated">
                    Laatst geüpdatet: {{ $schedule->last_updated ? $schedule->last_updated->format('d-m-Y H:i') : 'Nooit' }}
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Functionaliteit van de modusknop
document.querySelectorAll('.mode-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const daySchedule = this.closest('[data-day]');
        const day = daySchedule.dataset.day;
        const mode = this.dataset.mode;
        
        // Update actieve knop
        daySchedule.querySelectorAll('.mode-btn').forEach(b => {
            b.classList.remove('active');
        });
        this.classList.add('active');
        
        // Update verborgen invoer
        daySchedule.querySelector('input[name*="[mode]"]').value = mode;
        
        // Update statusbadge
        updateStatusBadge(day, mode);
        
        // Toon/verberg tijdinvoeren
        const splitInputs = daySchedule.querySelector('.time-input-group.split');
        const fullInputs = daySchedule.querySelector('.time-input-group.full');
        
        if (splitInputs) splitInputs.style.display = 'none';
        if (fullInputs) fullInputs.style.display = 'none';
        
        if (mode === 'split' && splitInputs) {
            splitInputs.style.display = 'grid';
        } else if (mode === 'fullday' && fullInputs) {
            fullInputs.style.display = 'grid';
        }
    });
});

// Functie om statusbadge bij te werken
function updateStatusBadge(day, mode) {
    const statusBadge = document.querySelector(`.day-status-badge[data-day="${day}"]`);
    if (statusBadge) {
        let statusText = '';
        let statusColor = '';
        
        switch(mode) {
            case 'closed':
                statusText = 'Gesloten';
                statusColor = '#dc3545';
                break;
            case 'open24':
                statusText = '24 Uur';
                statusColor = '#28a745';
                break;
            case 'split':
                statusText = 'Gesplitste Uren';
                statusColor = 'var(--green-accent)';
                break;
            case 'fullday':
                statusText = 'Overdag';
                statusColor = '#17a2b8';
                break;
            default:
                statusText = 'Gesplitste Uren';
                statusColor = 'var(--green-accent)';
        }
        
        statusBadge.textContent = statusText;
        statusBadge.style.color = statusColor;
    }
}

// Feestdagen beheer
function addHoliday() {
    const container = document.getElementById('holidays-list');
    const div = document.createElement('div');
    div.className = 'holiday-item';
    div.innerHTML = `
        <input type="date" name="holidays[]" value="" class="holiday-input">
        <button type="button" onclick="removeHoliday(this)" class="btn-remove">Verwijderen</button>
    `;
    container.appendChild(div);
}

function removeHoliday(btn) {
    btn.closest('.holiday-item').remove();
}

// Vakanties beheer
function addVacation() {
    const container = document.getElementById('vacations-list');
    const index = container.children.length;
    const div = document.createElement('div');
    div.className = 'vacation-item';
    div.innerHTML = `
        <div style="flex: 1; display: flex; flex-direction: column; gap: 0.5rem;">
            <div style="display: flex; gap: 0.5rem;">
                <input type="date" name="vacations[${index}][from]" value="" placeholder="Van" class="vacation-input" style="flex: 1;">
                <input type="date" name="vacations[${index}][to]" value="" placeholder="Tot" class="vacation-input" style="flex: 1;">
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <input type="text" name="vacations[${index}][note]" value="" placeholder="Opmerking" class="vacation-input">
                <button type="button" onclick="removeVacation(this)" class="btn-remove">Verwijderen</button>
            </div>
        </div>
    `;
    container.appendChild(div);
}

function removeVacation(btn) {
    btn.closest('.vacation-item').remove();
}

// Schema opslaan
function saveSchedule() {
    // Verzamel feestdagen
    const holidays = [];
    document.querySelectorAll('#holidays-list input[type="date"]').forEach(input => {
        if (input.value) holidays.push(input.value);
    });
    
    // Verzamel vakanties
    const vacations = [];
    document.querySelectorAll('#vacations-list > .vacation-item').forEach(item => {
        // Use name attribute for more reliable selection
        const fromInput = item.querySelector('input[name*="[from]"]');
        const toInput = item.querySelector('input[name*="[to]"]');
        const noteInput = item.querySelector('input[name*="[note]"]');
        
        if (fromInput && toInput) {
            const from = fromInput.value;
            const to = toInput.value;
            const note = noteInput ? noteInput.value : '';
            
            if (from && to) {
                vacations.push({ from, to, note });
            }
        }
    });
    
    // Bereid gegevens voor
    const data = {
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        schedule_data: {},
        holidays: holidays,
        vacations: vacations
    };
    
    // Verzamel schema gegevens
    document.querySelectorAll('[data-day]').forEach(daySchedule => {
        const day = daySchedule.dataset.day;
        const modeInput = daySchedule.querySelector('input[name*="[mode]"]');
        
        if (!modeInput) {
            console.warn(`Mode input not found for day: ${day}`);
            return;
        }
        
        const mode = modeInput.value;
        data.schedule_data[day] = { mode: mode };
        
        if (mode === 'split') {
            const o1Input = daySchedule.querySelector('input[name*="[split][o1]"]');
            const c1Input = daySchedule.querySelector('input[name*="[split][c1]"]');
            const o2Input = daySchedule.querySelector('input[name*="[split][o2]"]');
            const c2Input = daySchedule.querySelector('input[name*="[split][c2]"]');
            
            if (o1Input && c1Input && o2Input && c2Input) {
                data.schedule_data[day].split = {
                    o1: o1Input.value,
                    c1: c1Input.value,
                    o2: o2Input.value,
                    c2: c2Input.value
                };
            }
        } else if (mode === 'fullday') {
            const oInput = daySchedule.querySelector('input[name*="[full][o]"]');
            const cInput = daySchedule.querySelector('input[name*="[full][c]"]');
            
            if (oInput && cInput) {
                data.schedule_data[day].full = {
                    o: oInput.value,
                    c: cInput.value
                };
            }
        }
    });
    
    // Toon laden
    const saveBtn = document.querySelector('button[onclick="saveSchedule()"]');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Opslaan...';
    saveBtn.disabled = true;
    
    // Verzenden verzoek
    fetch('{{ route("service-provider.schedule.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': data._token,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            showMessage('Schema succesvol opgeslagen!', 'success');
            // Redirect to dashboard after 2 seconds to allow user to see success message
            setTimeout(() => {
                window.location.href = '{{ route("service-provider.dashboard") }}';
            }, 2000);
        } else {
            showMessage('Fout bij het opslaan van het schema: ' + (result.message || 'Onbekende fout'), 'error');
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Fout:', error);
        showMessage('Fout bij het opslaan van het schema: ' + (error.message || 'Onbekende fout'), 'error');
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// Toon bericht
function showMessage(message, type) {
    const container = document.getElementById('message-container');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    container.innerHTML = '';
    container.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.remove();
    }, 5000);
}
</script>

@endsection
 