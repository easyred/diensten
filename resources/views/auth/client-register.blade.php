@php
    // Get category data if available (similar to welcome page)
    $cat = $category ?? null;
    $primaryColor = $cat && $cat->primary_color ? $cat->primary_color : '#0284c7';
    $secondaryColor = $cat && $cat->secondary_color ? $cat->secondary_color : '#0369a1';
    $logo = $cat && $cat->logo_url ? (str_starts_with($cat->logo_url, 'http') ? $cat->logo_url : asset($cat->logo_url)) : null;
    $brandName = $cat ? $cat->name : 'diensten';
    $brandTagline = $cat && $cat->site_description ? $cat->site_description : 'Professionele Diensten';
    $mainIcon = 'fa-briefcase';
    
    // Favicon
    if ($cat && $cat->favicon_url) {
        $favicon = str_starts_with($cat->favicon_url, 'http') ? $cat->favicon_url : asset($cat->favicon_url);
    } else {
        $iconColor = $primaryColor;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512"><path fill="' . htmlspecialchars($iconColor, ENT_QUOTES, 'UTF-8') . '" d="M184 48h144c4.4 0 8 3.6 8 8V96H176V56c0-4.4 3.6-8 8-8zm-56 8V96H64C28.7 96 0 124.7 0 160v96H192 320 512V160c0-35.3-28.7-64-64-64H384V56c0-30.9-25.1-56-56-56H184c-30.9 0-56 25.1-56 56zM512 288H320v32c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288H0v128c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V288z"/></svg>';
        $favicon = 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
@endphp
<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Maak Klantenaccount - {{ $brandName }}.pro</title>
    <meta name="description" content="Maak een klantenaccount aan op {{ $brandName }}.pro">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ $favicon }}">
    <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $favicon }}">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- intl-tel-input for country code selector -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '{{ $primaryColor }}10',
                            100: '{{ $primaryColor }}20',
                            500: '{{ $primaryColor }}',
                            600: '{{ $primaryColor }}',
                            700: '{{ $secondaryColor }}',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
        }
        .gradient-bg {
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
        }
        .btn-primary {
            background-color: {{ $primaryColor }};
        }
        .btn-primary:hover {
            background-color: {{ $secondaryColor }};
        }
        
        /* intl-tel-input styling */
        .phone-control .iti {
            width: 100% !important;
        }
        .phone-control .iti__selected-flag {
            padding: 0 8px 0 12px;
        }
        .phone-control .iti input {
            padding-left: 70px !important;
        }
        
        /* Address suggestions */
        .suggest {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-top: 4px;
            box-shadow: 0 10px 24px rgba(0,0,0,.15);
            z-index: 50;
            max-height: 240px;
            overflow: auto;
            display: none;
        }
        .s-item {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            display: flex;
            gap: 8px;
            align-items: flex-start;
            transition: background .2s;
        }
        .s-item:last-child {
            border-bottom: 0;
        }
        .s-item:hover,
        .s-item.active {
            background: rgba(2, 132, 199, 0.08);
        }
        .s-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 6px;
            border: 1px solid rgba(2, 132, 199, 0.3);
            background: rgba(2, 132, 199, 0.1);
            color: #0284c7;
            white-space: nowrap;
            font-weight: 700;
        }
        .s-label {
            flex: 1;
            color: #1f2937;
            font-size: 13px;
            line-height: 1.4;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50 backdrop-blur-sm bg-opacity-95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);">
                        @if($logo)
                            <img src="{{ $logo }}" alt="{{ $brandName }}" class="w-10 h-10 md:w-12 md:h-12 rounded-xl object-cover" onerror="this.style.display='none'; this.parentElement.querySelector('.logo-icon-fallback').style.display='flex';">
                            <i class="fas {{ $mainIcon }} text-white text-lg md:text-xl logo-icon-fallback" style="display: none;"></i>
                        @else
                            <i class="fas {{ $mainIcon }} text-white text-lg md:text-xl"></i>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('welcome') }}" class="text-lg md:text-2xl font-bold text-gray-900 hover:text-primary-600 transition" style="--hover-color: {{ $primaryColor }};">
                            {{ $brandName }}<span class="text-primary-600" style="color: {{ $primaryColor }};">.pro</span>
                        </a>
                        <p class="text-xs text-gray-500 -mt-1 hidden sm:block">{{ $brandTagline }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="hidden sm:inline-block text-gray-700 hover:text-primary-600 px-4 py-2 rounded-lg font-medium transition">Inloggen</a>
                        <a href="{{ route('register') }}" class="bg-primary-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-700 transition shadow-lg font-semibold text-sm sm:text-base" style="background-color: {{ $primaryColor }};" onmouseover="this.style.backgroundColor='{{ $secondaryColor }}'" onmouseout="this.style.backgroundColor='{{ $primaryColor }}'">
                            Service Provider
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-primary-600 px-4 py-2 rounded-lg font-medium transition">Dashboard</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Client Register Section -->
    <section class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $brandName }}" class="w-20 h-20 rounded-2xl object-cover" onerror="this.style.display='none'; this.parentElement.querySelector('.header-logo-icon-fallback').style.display='flex';">
                        <i class="fas {{ $mainIcon }} text-white text-3xl header-logo-icon-fallback" style="display: none;"></i>
                    @else
                        <i class="fas {{ $mainIcon }} text-white text-3xl"></i>
                    @endif
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Maak Klantenaccount</h1>
                <p class="text-base sm:text-lg text-gray-600">Verbind met gekwalificeerde service providers in uw omgeving</p>
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-6">
                    {{ session('success') }}
                </div>
            @endif
            @if (isset($errors) && $errors && $errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm mb-6">
                    Gelieve de hieronder gemarkeerde velden te corrigeren.
                </div>
            @endif

            <!-- Register Form -->
            <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-100">
                <form method="POST" action="{{ route('client.register.store') }}" class="space-y-6" novalidate>
                    @csrf

                    <!-- Honeypot field - hidden from users, bots will fill it -->
                    <div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                        <label for="website">Website (laat leeg)</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <!-- UW GEGEVENS -->
                    <fieldset class="border border-gray-200 rounded-xl p-4 sm:p-6 bg-gray-50">
                        <legend class="text-xs font-bold uppercase tracking-wider text-gray-700 px-2">Uw Gegevens</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <!-- Full Name -->
                            <div class="sm:col-span-2">
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Volledige naam *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input id="full_name" 
                                           name="full_name" 
                                           value="{{ old('full_name') }}" 
                                           placeholder="Jan Jansen" 
                                           required
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                           style="--tw-ring-color: {{ $primaryColor }};">
                                </div>
                                @error('full_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- WhatsApp Number -->
                            <div class="sm:col-span-2">
                                <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">WhatsApp nummer *</label>
                                <div class="relative phone-control">
                                    <input id="whatsapp_number" 
                                           name="whatsapp_number" 
                                           type="tel" 
                                           value="{{ old('whatsapp_number') }}" 
                                           required
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                           style="--tw-ring-color: {{ $primaryColor }};">
                                </div>
                                @error('whatsapp_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Company Name -->
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Bedrijfsnaam <span class="text-gray-500 text-xs">(optioneel)</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-building text-gray-400"></i>
                                    </div>
                                    <input id="company_name" 
                                           name="company_name" 
                                           value="{{ old('company_name') }}" 
                                           placeholder="Laat leeg voor persoonlijk account"
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                           style="--tw-ring-color: {{ $primaryColor }};">
                                </div>
                                @error('company_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input id="email" 
                                           type="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="u@example.com" 
                                           required
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                           style="--tw-ring-color: {{ $primaryColor }};">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Wachtwoord *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input id="password" 
                                           type="password" 
                                           name="password" 
                                           placeholder="••••••••" 
                                           required
                                           class="block w-full pl-10 pr-20 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                           style="--tw-ring-color: {{ $primaryColor }};">
                                    <button type="button" 
                                            onclick="togglePassword()" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                                        <span id="toggle-text">Toon</span>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Bevestig wachtwoord *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input id="password_confirmation" 
                                           type="password" 
                                           name="password_confirmation" 
                                           placeholder="••••••••" 
                                           required
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                           style="--tw-ring-color: {{ $primaryColor }};">
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    <!-- ADRES -->
                    <fieldset class="border border-gray-200 rounded-xl p-4 sm:p-6 bg-gray-50">
                        <legend class="text-xs font-bold uppercase tracking-wider text-gray-700 px-2">Adres</legend>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <!-- Address -->
                            <div class="sm:col-span-2 relative">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Straatadres *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-home text-gray-400"></i>
                                    </div>
                                    <input id="address" 
                                           name="address" 
                                           value="{{ old('address') }}" 
                                           placeholder="Begin met typen straat, nummer, stad…" 
                                           autocomplete="off" 
                                           required
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                           style="--tw-ring-color: {{ $primaryColor }};">
                                    <input type="hidden" id="address_json" name="address_json" value="{{ old('address_json') }}">
                                </div>
                                <div id="suggest" class="suggest"></div>
                                <p class="mt-1 text-xs text-gray-500">Begin te typen en kies een adres om de velden automatisch in te vullen.</p>
                            </div>

                            <!-- House Number -->
                            <div>
                                <label for="number" class="block text-sm font-medium text-gray-700 mb-2">Huisnummer</label>
                                <input id="number" 
                                       name="number" 
                                       value="{{ old('number') }}" 
                                       placeholder="12A"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                       style="--tw-ring-color: {{ $primaryColor }};">
                                @error('number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Postal Code -->
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postcode *</label>
                                <input id="postal_code" 
                                       name="postal_code" 
                                       value="{{ old('postal_code') }}" 
                                       placeholder="1000"
                                       required
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                       style="--tw-ring-color: {{ $primaryColor }};">
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- City -->
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Stad *</label>
                                <input id="city" 
                                       name="city" 
                                       value="{{ old('city') }}" 
                                       placeholder="Brussel"
                                       required
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                       style="--tw-ring-color: {{ $primaryColor }};">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    <!-- Submit Buttons -->
                    <div class="space-y-3">
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-6 py-3 border border-transparent rounded-lg text-white font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition text-base"
                                style="background-color: {{ $primaryColor }};" 
                                onmouseover="this.style.backgroundColor='{{ $secondaryColor }}'" 
                                onmouseout="this.style.backgroundColor='{{ $primaryColor }}'">
                            <i class="fas fa-user-plus mr-2"></i>
                            Maak klantenaccount
                        </button>
                        <a href="{{ route('login') }}" 
                           class="w-full flex items-center justify-center px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition text-base">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Ik heb al een account
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleText = document.getElementById('toggle-text');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleText.textContent = 'Verberg';
            } else {
                passwordInput.type = 'password';
                toggleText.textContent = 'Toon';
            }
        }

        // Initialize intl-tel-input for WhatsApp number
        document.addEventListener('DOMContentLoaded', function() {
            const whatsappInput = document.getElementById('whatsapp_number');
            if (whatsappInput) {
                const iti = window.intlTelInput(whatsappInput, {
                    initialCountry: "be",
                    preferredCountries: ["be", "nl", "fr", "de", "uk"],
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js"
                });

                const form = whatsappInput.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const fullNumber = iti.getNumber(intlTelInputUtils.numberFormat.E164);
                        if (fullNumber) {
                            whatsappInput.value = fullNumber;
                        }
                    });
                }
            }

            // Address search functionality
            const input = document.querySelector('#address');
            const sugg = document.querySelector('#suggest');
            const number = document.querySelector('#number');
            const zip = document.querySelector('#postal_code');
            const city = document.querySelector('#city');
            const hidden = document.querySelector('#address_json');
            
            if (!input || !sugg || !number || !zip || !city || !hidden) { 
                return; 
            }
            
            let items = []; 
            let activeIndex = -1; 
            let debounceId = null;

            async function fetchJSON(url) {
                const r = await fetch(url);
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            }

            async function searchSmart(qUser) {
                try {
                    const results = await fetchJSON(`{{ route('address.suggest') }}?q=${encodeURIComponent(qUser)}&c=10`);
                    if (Array.isArray(results) && results.length > 0) {
                        const hasVLStructure = results.some(item => item.Suggestion || item._vlLoc);
                        return {data: results, src: hasVLStructure ? 'vl' : 'osm'};
                    }
                    return {data: [], src: 'vl'};
                } catch(e) {
                    console.error('Adres zoekfout:', e);
                    return {data: [], src: 'vl'};
                }
            }

            function escapeHtml(s) { 
                return (s || '').replace(/[&<>"]/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;"}[c])); 
            }

            function renderList(list, src) {
                if (!list || !list.length) {
                    sugg.innerHTML = `<div class="s-item"><span class="s-badge">Ø</span><span class="s-label">Geen resultaten gevonden</span></div>`;
                    sugg.style.display = 'block';
                    return;
                }
                
                sugg.innerHTML = list.map((it, i) => {
                    const label = (src === 'vl') ? (it?.Suggestion?.Label || '') : (it?.display_name || '');
                    const b = (src === 'vl') ? 'VL' : 'OSM';
                    return `<div class="s-item${i === activeIndex ? ' active' : ''}" data-i="${i}" data-src="${b}">
                        <span class="s-badge">[${b}]</span>
                        <span class="s-label">${escapeHtml(label)}</span>
                    </div>`;
                }).join('');
                
                sugg.style.display = 'block';
                sugg.querySelectorAll('.s-item').forEach(el => {
                    el.addEventListener('mousedown', () => choose(parseInt(el.dataset.i, 10), el.dataset.src));
                });
            }

            async function choose(i, srcBadge) {
                if (i < 0 || i >= items.length) return;
                const src = (srcBadge === 'VL') ? 'vl' : 'osm';
                const label = (src === 'vl') ? (items[i]?.Suggestion?.Label || '') : (items[i]?.display_name || '');
                input.value = label;
                sugg.style.display = 'none';

                try {
                    if (src === 'vl') {
                        if (items[i] && items[i]._osmData) {
                            showOSM(items[i]._osmData);
                        } else if (items[i] && items[i]._vlLoc) {
                            showVL(items[i]._vlLoc.Location, items[i]._vlLoc);
                        } else {
                            const detailedResults = await fetchJSON(`{{ route('address.suggest') }}?q=${encodeURIComponent(label)}&detailed=1`);
                            if (detailedResults && detailedResults.length > 0 && detailedResults[0]._vlLoc) {
                                showVL(detailedResults[0]._vlLoc.Location, detailedResults[0]._vlLoc);
                            } else {
                                const osmResults = await fetchJSON(`{{ route('address.suggest') }}?q=${encodeURIComponent(label)}&osm=1`);
                                if (osmResults && osmResults.length > 0) {
                                    showOSM(osmResults[0]);
                                } else {
                                    showVLFromLabel(label);
                                }
                            }
                        }
                    } else {
                        showOSM(items[i]);
                    }
                } catch(e) {
                    console.error('Fout bij het ophalen van details:', e);
                }
            }

            function showVL(L, rawArr) {
                if (L) {
                    const streetName = L.Thoroughfarename || '';
                    const houseNumber = L.Housenumber || '';
                    const postalCode = L.Postalcode || '';
                    const municipality = L.Municipality || '';
                    input.value = [streetName, houseNumber].filter(Boolean).join(' ');
                    number.value = houseNumber;
                    zip.value = postalCode;
                    city.value = municipality;
                    hidden.value = JSON.stringify({source: 'vl', location: L, raw: rawArr});
                }
            }

            function showOSM(it) {
                const addr = it?.address || {};
                const streetName = addr.road || addr.pedestrian || addr.path || '';
                const houseNumber = addr.house_number || '';
                const postalCode = addr.postcode || '';
                const cityName = addr.village || addr.town || addr.city || addr.municipality || '';
                input.value = [streetName, houseNumber].filter(Boolean).join(' ');
                number.value = houseNumber;
                zip.value = postalCode;
                city.value = cityName;
                hidden.value = JSON.stringify({source: 'osm', address: it});
            }

            function showVLFromLabel(label) {
                const parts = label.split(', ');
                if (parts.length >= 2) {
                    const streetPart = parts[0].trim();
                    const cityPart = parts[1].trim();
                    input.value = streetPart;
                    city.value = cityPart;
                }
            }

            input.addEventListener('input', function(e) {
                const q = e.target.value.trim();
                if (q.length < 2) {
                    sugg.style.display = 'none';
                    return;
                }
                clearTimeout(debounceId);
                debounceId = setTimeout(async () => {
                    const {data, src} = await searchSmart(q);
                    items = data;
                    activeIndex = -1;
                    renderList(data, src);
                }, 300);
            });

            input.addEventListener('blur', function() {
                setTimeout(() => { sugg.style.display = 'none'; }, 200);
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeIndex = Math.min(activeIndex + 1, items.length - 1);
                    renderList(items, 'vl');
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeIndex = Math.max(activeIndex - 1, -1);
                    renderList(items, 'vl');
                } else if (e.key === 'Enter' && activeIndex >= 0) {
                    e.preventDefault();
                    choose(activeIndex, 'VL');
                }
            });
        });
    </script>
</body>
</html>
