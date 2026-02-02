@php
    // Set defaults for when category is null (diensten.pro default)
    $defaultContent = [
        'brand_name' => 'diensten',
        'brand_tagline' => 'Professionele Diensten',
        'hero_title' => 'Vind de Perfecte',
        'hero_title_highlight' => 'Professionele Dienst',
        'hero_subtitle' => 'Eén platform voor alle professionele diensten. Van loodgieters tot tuinmannen, vind en boek betrouwbare professionals in uw buurt. Eén account, toegang tot alle diensten.',
        'hero_cta_primary' => 'Start Nu Gratis',
        'hero_cta_secondary' => 'Klant Account',
        'hero_cta_tertiary' => 'Bekijk Diensten',
        'hero_features' => ['Gratis Aanmelden', 'Geen Verborgen Kosten', '24/7 Beschikbaar'],
    ];
    
    // Get category data or use defaults
    // On main domain (diensten.pro), don't use category-specific content
    $host = request()->getHost();
    $cleanHost = preg_replace('/^www\./', '', $host);
    $isMainDomain = $cleanHost === 'diensten.pro' || $host === 'localhost' || str_contains($host, '127.0.0.1');
    
    $cat = $category ?? null;
    // Only merge category content if NOT on main domain
    if ($isMainDomain) {
        $content = $defaultContent;
        // Keep $cat reference for OG image, logo, etc. but don't use its welcome_content
    } else {
        $categoryContent = ($cat && $cat->welcome_content && !empty($cat->welcome_content)) ? $cat->welcome_content : [];
        $content = array_merge($defaultContent, $categoryContent);
    }
    $primaryColor = $cat && $cat->primary_color ? $cat->primary_color : '#0284c7';
    $secondaryColor = $cat && $cat->secondary_color ? $cat->secondary_color : '#0369a1';
    $metaTitle = $cat && $cat->meta_title ? $cat->meta_title : ($cat ? $cat->name . ' - ' . $cat->domain : 'diensten.pro - Uw Platform voor Professionele Diensten');
    $metaDescription = $cat && $cat->meta_description ? $cat->meta_description : ($cat && $cat->site_description ? $cat->site_description : 'Vind en boek professionele diensten zoals loodgieters, tuinmannen en meer. Eén account, toegang tot alle diensten.');
    $logo = $cat && $cat->logo_url ? (str_starts_with($cat->logo_url, 'http') ? $cat->logo_url : asset($cat->logo_url)) : null;
    $brandName = $content['brand_name'] ?? ($cat ? $cat->name : 'diensten');
    $brandTagline = $content['brand_tagline'] ?? ($cat ? $cat->site_description : 'Professionele Diensten');
    // Default icon for main domain (diensten.pro)
    $mainIcon = 'fa-briefcase';
    
    // Favicon: use category favicon if available, otherwise create SVG from icon
    if ($cat && $cat->favicon_url && !$isMainDomain) {
        $favicon = str_starts_with($cat->favicon_url, 'http') ? $cat->favicon_url : asset($cat->favicon_url);
    } else {
        // Create SVG favicon from briefcase icon for main domain
        $iconColor = $primaryColor;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512"><path fill="' . htmlspecialchars($iconColor, ENT_QUOTES, 'UTF-8') . '" d="M184 48h144c4.4 0 8 3.6 8 8V96H176V56c0-4.4 3.6-8 8-8zm-56 8V96H64C28.7 96 0 124.7 0 160v96H192 320 512V160c0-35.3-28.7-64-64-64H384V56c0-30.9-25.1-56-56-56H184c-30.9 0-56 25.1-56 56zM512 288H320v32c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288H0v128c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V288z"/></svg>';
        $favicon = 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    // OG Image: Check for uploaded OG image first, then generate if needed
    if ($cat && $cat->og_image_url) {
        // Use admin-uploaded OG image (works for both main domain and category domains)
        $ogImage = str_starts_with($cat->og_image_url, 'http') ? $cat->og_image_url : asset($cat->og_image_url);
    } else if ($isMainDomain) {
        // Main domain without uploaded image: generate OG image with generic "diensten" category code
        $ogImage = route('og-image', ['category' => 'diensten', 'color' => urlencode($primaryColor)]);
    } else if ($cat) {
        // Category domain without admin-set OG image: generate from category icon
        $ogImage = route('og-image', ['category' => $cat->code, 'color' => urlencode($primaryColor)]);
    } else {
        // Fallback: generate default OG image
        $ogImage = route('og-image', ['category' => 'diensten', 'color' => urlencode($primaryColor)]);
    }
@endphp
<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @if($cat && $cat->meta_keywords)
        <meta name="keywords" content="{{ $cat->meta_keywords }}">
    @endif
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/png">
    <meta property="og:site_name" content="{{ $brandName }}.pro">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
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
        .service-card {
            transition: all 0.3s ease;
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .nav-link {
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            color: {{ $primaryColor }};
        }
        .gradient-bg {
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
        }
        .feature-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
            color: white;
            font-size: 20px;
        }
        @media (min-width: 768px) {
            .feature-icon {
                width: 60px;
                height: 60px;
                font-size: 24px;
            }
        }
        .btn-primary {
            background-color: {{ $primaryColor }};
        }
        .btn-primary:hover {
            background-color: {{ $secondaryColor }};
        }
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 0;
        }
        .faq-answer:not(.hidden) {
            max-height: 500px;
            opacity: 1;
        }
        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50 backdrop-blur-sm bg-opacity-95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-20">
                <div class="flex items-center space-x-2 md:space-x-3">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center shadow-lg main-logo-container" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);">
                        @if($logo)
                            <img src="{{ $logo }}" alt="{{ $brandName }}" class="w-10 h-10 md:w-12 md:h-12 rounded-xl object-cover main-logo-image" onerror="this.style.display='none'; this.parentElement.querySelector('.main-logo-icon-fallback').style.display='flex';">
                            <i class="fas {{ $mainIcon }} text-white text-lg md:text-xl main-logo-icon-fallback" style="display: none;"></i>
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
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#services" class="nav-link text-gray-700 font-medium">Diensten</a>
                    <a href="#how-it-works" class="nav-link text-gray-700 font-medium">Hoe het werkt</a>
                    <a href="#features" class="nav-link text-gray-700 font-medium">Voordelen</a>
                    <a href="#pricing" class="nav-link text-gray-700 font-medium">Prijzen</a>
                </div>
                <!-- Desktop Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 px-4 py-2 rounded-lg font-medium transition">Inloggen</a>
                        <a href="{{ route('register', $cat ? ['category' => $cat->code] : []) }}" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition shadow-lg font-semibold">
                            Gratis Aanmelden
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-primary-600 px-4 py-2 rounded-lg font-medium transition">Dashboard</a>
                        @if(auth()->user()->role === 'super_admin')
                            <a href="{{ route('admin.dashboard') }}" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition font-semibold">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-primary-600 px-4 py-2 rounded-lg font-medium transition">Uitloggen</button>
                        </form>
                    @endguest
                </div>
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-primary-600 transition p-2" aria-label="Toggle menu">
                    <i class="fas fa-bars text-xl" id="menu-icon"></i>
                </button>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-gray-200 mt-2">
                <div class="flex flex-col space-y-2 pt-4">
                    <a href="#services" class="nav-link text-gray-700 font-medium px-2 py-2">Diensten</a>
                    <a href="#how-it-works" class="nav-link text-gray-700 font-medium px-2 py-2">Hoe het werkt</a>
                    <a href="#features" class="nav-link text-gray-700 font-medium px-2 py-2">Voordelen</a>
                    <a href="#pricing" class="nav-link text-gray-700 font-medium px-2 py-2">Prijzen</a>
                    <div class="border-t border-gray-200 pt-2 mt-2">
                        @guest
                            <a href="{{ route('login') }}" class="block text-gray-700 hover:text-primary-600 px-2 py-2 font-medium transition">Inloggen</a>
                            <a href="{{ route('register', $cat ? ['category' => $cat->code] : []) }}" class="block bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition shadow-lg font-semibold mt-2 text-center">
                                Gratis Aanmelden
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="block text-gray-700 hover:text-primary-600 px-2 py-2 font-medium transition">Dashboard</a>
                            @if(auth()->user()->role === 'super_admin')
                                <a href="{{ route('admin.dashboard') }}" class="block bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition font-semibold mt-2 text-center">Admin</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                @csrf
                                <button type="submit" class="w-full text-left text-gray-700 hover:text-primary-600 px-2 py-2 font-medium transition">Uitloggen</button>
                            </form>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-12 md:py-24 relative overflow-hidden" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-64 h-64 md:w-96 md:h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-64 h-64 md:w-96 md:h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight px-2">
                    {{ $content['hero_title'] ?? 'Vind de Perfecte' }}<br>
                    <span class="text-yellow-300">{{ $content['hero_title_highlight'] ?? 'Professionele Dienst' }}</span>
                </h1>
                <p class="text-base sm:text-lg md:text-xl text-gray-100 mb-6 md:mb-10 max-w-3xl mx-auto leading-relaxed px-2">
                    {{ $content['hero_subtitle'] ?? ($cat && $cat->site_description ? $cat->site_description : 'Eén platform voor alle professionele diensten. Van loodgieters tot tuinmannen, vind en boek betrouwbare professionals in uw buurt. Eén account, toegang tot alle diensten.') }}
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-stretch sm:items-center gap-3 sm:gap-4 px-2">
                    <a href="{{ route('register', $cat ? ['category' => $cat->code] : []) }}" class="bg-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-bold hover:bg-gray-100 transition shadow-2xl transform hover:scale-105 text-center" style="color: {{ $primaryColor }};">
                        <i class="fas fa-rocket mr-2"></i>{{ $content['hero_cta_primary'] ?? 'Start Nu Gratis' }}
                    </a>
                    <a href="{{ route('client.register') }}" class="bg-transparent border-2 border-white text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold hover:bg-white transition text-center" style="--hover-color: {{ $primaryColor }};">
                        <i class="fas fa-user mr-2"></i>{{ $content['hero_cta_secondary'] ?? 'Klant Account' }}
                    </a>
                    <a href="#services" class="bg-transparent border-2 border-white text-white px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold hover:bg-white transition text-center" style="--hover-color: {{ $primaryColor }};">
                        <i class="fas fa-search mr-2"></i>{{ $content['hero_cta_tertiary'] ?? 'Bekijk Diensten' }}
                    </a>
                </div>
                @if(isset($content['hero_features']) && is_array($content['hero_features']))
                <div class="mt-8 md:mt-12 flex flex-col sm:flex-row justify-center items-center gap-4 sm:gap-6 md:space-x-8 text-xs sm:text-sm px-2">
                    @foreach($content['hero_features'] as $feature)
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-check-circle text-yellow-300"></i>
                        <span>{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-12 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 md:mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 md:mb-4 px-2">{{ $content['services_title'] ?? 'Onze Diensten' }}</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                    {{ $content['services_subtitle'] ?? 'Kies uit ons uitgebreide aanbod van professionele dienstverleners. Elke dienst wordt geleverd door geverifieerde professionals.' }}
                </p>
            </div>
            
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                    @foreach($categories as $catItem)
                        @php
                            // Icon mapping for categories
                            $categoryIcons = [
                                'plumber' => 'fa-wrench',
                                'loodgieter' => 'fa-wrench',
                                'computerhulp' => 'fa-laptop',
                                'dakwerken' => 'fa-home',
                                'dierenarts' => 'fa-paw',
                                'elektrieker' => 'fa-bolt',
                                'gaslek' => 'fa-fire',
                                'glazenmaker' => 'fa-window-maximize',
                                'ithulp' => 'fa-server',
                                'klusjes' => 'fa-hammer',
                                'koeling' => 'fa-snowflake',
                                'kuisvrouw' => 'fa-broom',
                                'ontstopping' => 'fa-tint',
                                'opkuis' => 'fa-broom',
                                'slotenmaker' => 'fa-key',
                                'thuiszorg' => 'fa-heart',
                                'tuinman' => 'fa-leaf',
                                'gardener' => 'fa-leaf',
                                'verhuis' => 'fa-truck',
                            ];
                            $iconClass = $categoryIcons[$catItem->code] ?? 'fa-tools';
                            $logoUrl = $catItem->logo_url ? (str_starts_with($catItem->logo_url, 'http') ? $catItem->logo_url : asset($catItem->logo_url)) : null;
                        @endphp
                        <div class="service-card bg-white rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 border border-gray-100 hover:border-primary-200" style="--hover-border-color: {{ $primaryColor }};">
                            <div class="text-center">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mx-auto mb-4 md:mb-6 rounded-2xl flex items-center justify-center shadow-lg category-icon-container" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);" data-icon="{{ $iconClass }}">
                                    @if($logoUrl)
                                        <img src="{{ $logoUrl }}" alt="{{ $catItem->name }}" class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-2xl object-cover category-logo" onerror="this.style.display='none'; this.parentElement.querySelector('.category-icon-fallback').style.display='flex';">
                                        <i class="fas {{ $iconClass }} text-white text-2xl sm:text-3xl md:text-4xl category-icon-fallback" style="display: none;"></i>
                                    @else
                                        <i class="fas {{ $iconClass }} text-white text-2xl sm:text-3xl md:text-4xl"></i>
                                    @endif
                                </div>
                                <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2 md:mb-3">{{ $catItem->name }}</h3>
                                <p class="text-sm sm:text-base text-gray-600 mb-4 md:mb-6 leading-relaxed">
                                    Professionele {{ strtolower($catItem->name) }} diensten van geverifieerde experts in uw regio.
                                </p>
                                <div class="space-y-2 md:space-y-3">
                                    <a href="{{ route('register', ['category' => $catItem->code]) }}" class="block w-full text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition font-semibold shadow-lg text-sm sm:text-base" style="background-color: {{ $primaryColor }};" onmouseover="this.style.backgroundColor='{{ $secondaryColor }}'" onmouseout="this.style.backgroundColor='{{ $primaryColor }}'">
                                        <i class="fas fa-user-plus mr-2"></i>Aanmelden voor {{ $catItem->name }}
                                    </a>
                                    @if($catItem->domain)
                                        <a href="http://{{ $catItem->domain }}" target="_blank" class="block w-full bg-gray-100 text-gray-700 px-4 sm:px-6 py-2 rounded-lg hover:bg-gray-200 transition text-xs sm:text-sm">
                                            <i class="fas fa-external-link-alt mr-2"></i>Bezoek {{ $catItem->name }} Website
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 md:py-16">
                    <i class="fas fa-info-circle text-gray-400 text-4xl sm:text-5xl md:text-6xl mb-3 md:mb-4"></i>
                    <p class="text-gray-600 text-base sm:text-lg md:text-xl px-2">Er zijn nog geen diensten beschikbaar. Binnenkort meer!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-12 md:py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 md:mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 md:mb-4 px-2">{{ $content['how_it_works_title'] ?? 'Hoe Het Werkt' }}</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                    {{ $content['how_it_works_subtitle'] ?? 'In drie eenvoudige stappen vindt u de perfecte professional voor uw behoeften.' }}
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                @php
                    $steps = $content['how_it_works_steps'] ?? [
                        ['title' => 'Maak een Account', 'description' => 'Registreer gratis en kies uw gewenste diensten. Eén account geeft toegang tot alle platforms.', 'icon' => 'fa-user-plus', 'link' => route('client.register'), 'link_text' => 'Of maak een klant account'],
                        ['title' => 'Zoek Professionals', 'description' => 'Blader door geverifieerde professionals in uw buurt en bekijk hun profielen en beoordelingen.', 'icon' => 'fa-search'],
                        ['title' => 'Boek & Geniet', 'description' => 'Boek direct via WhatsApp of het platform. Ontvang updates en geniet van professionele service.', 'icon' => 'fa-handshake'],
                    ];
                @endphp
                @foreach($steps as $index => $step)
                <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 text-center relative">
                    <div class="absolute -top-4 md:-top-6 left-1/2 transform -translate-x-1/2 w-10 h-10 md:w-12 md:h-12 text-white rounded-full flex items-center justify-center font-bold text-lg md:text-xl shadow-lg" style="background-color: {{ $primaryColor }};">
                        {{ $index + 1 }}
                    </div>
                    <div class="mt-4 md:mt-6 mb-4 md:mb-6">
                        <div class="feature-icon mx-auto">
                            <i class="fas {{ $step['icon'] ?? 'fa-check' }}"></i>
                        </div>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-3 md:mb-4">{{ $step['title'] ?? 'Stap ' . ($index + 1) }}</h3>
                    <p class="text-sm md:text-base text-gray-600 leading-relaxed mb-3 md:mb-4">
                        {{ $step['description'] ?? '' }}
                    </p>
                    @if(isset($step['link']) && isset($step['link_text']))
                    <a href="{{ $step['link'] }}" class="font-semibold text-xs md:text-sm" style="color: {{ $primaryColor }};">
                        <i class="fas fa-user mr-1"></i>{{ $step['link_text'] }}
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-12 md:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 md:mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 md:mb-4 px-2">{{ $content['features_title'] ?? 'Waarom ' . $brandName . '.pro?' }}</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                    {{ $content['features_subtitle'] ?? 'Het platform dat alles samenbrengt voor uw gemak en vertrouwen.' }}
                </p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8">
                @php
                    $features = $content['features'] ?? [
                        ['title' => 'Veilig & Betrouwbaar', 'description' => 'Uw gegevens zijn beschermd met enterprise-grade beveiliging', 'icon' => 'fa-shield-alt', 'color' => 'blue'],
                        ['title' => 'Universele Toegang', 'description' => 'Eén account werkt op alle service platforms', 'icon' => 'fa-users', 'color' => 'green'],
                        ['title' => 'Flexibele Abonnementen', 'description' => 'Eén abonnement, toegang tot alle diensten', 'icon' => 'fa-credit-card', 'color' => 'purple'],
                        ['title' => '24/7 Ondersteuning', 'description' => 'Altijd hulp wanneer u het nodig heeft', 'icon' => 'fa-headset', 'color' => 'orange'],
                    ];
                @endphp
                @foreach($features as $feature)
                <div class="bg-gradient-to-br rounded-2xl p-4 md:p-6 lg:p-8 text-center border" style="background: linear-gradient(135deg, {{ $primaryColor }}10 0%, {{ $secondaryColor }}10 100%); border-color: {{ $primaryColor }}30;">
                    <div class="w-12 h-12 md:w-14 md:h-14 lg:w-16 lg:h-16 rounded-xl flex items-center justify-center mx-auto mb-3 md:mb-4 shadow-lg" style="background-color: {{ $primaryColor }};">
                        <i class="fas {{ $feature['icon'] ?? 'fa-check' }} text-white text-lg md:text-xl lg:text-2xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ $feature['title'] ?? 'Feature' }}</h3>
                    <p class="text-gray-600 text-xs md:text-sm">{{ $feature['description'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-12 md:py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 md:mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 md:mb-4 px-2">{{ $content['pricing_title'] ?? 'Eenvoudige Prijzen' }}</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                    {{ $content['pricing_subtitle'] ?? 'Kies het abonnement dat bij u past. Eén betaling, toegang tot alle diensten.' }}
                </p>
            </div>
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl p-6 sm:p-8 md:p-12 text-center">
                <div class="mb-6 md:mb-8">
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 md:mb-4">{{ $content['pricing_plan_name'] ?? 'Standaard Abonnement' }}</h3>
                    <div class="flex items-baseline justify-center mb-4 md:mb-6">
                        <span class="text-4xl sm:text-5xl md:text-6xl font-bold" style="color: {{ $primaryColor }};">{{ $content['pricing_amount'] ?? '€29' }}</span>
                        <span class="text-xl sm:text-2xl text-gray-500 ml-2">{{ $content['pricing_period'] ?? '/maand' }}</span>
                    </div>
                    <p class="text-base sm:text-lg text-gray-600 mb-6 md:mb-8 px-2">
                        {{ $content['pricing_description'] ?? 'Volledige toegang tot alle diensten en platforms' }}
                    </p>
                </div>
                @php
                    $pricingFeatures = $content['pricing_features'] ?? [
                        'Toegang tot alle service platforms',
                        'Onbeperkt verzoeken indienen',
                        'Prioriteit ondersteuning',
                        'Geen verborgen kosten',
                    ];
                @endphp
                <div class="space-y-3 md:space-y-4 mb-6 md:mb-8 text-left max-w-md mx-auto">
                    @foreach($pricingFeatures as $feature)
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500 flex-shrink-0"></i>
                        <span class="text-sm sm:text-base">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('register', $cat ? ['category' => $cat->code] : []) }}" class="inline-block text-white px-6 sm:px-8 md:px-10 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-bold transition shadow-lg transform hover:scale-105" style="background-color: {{ $primaryColor }};" onmouseover="this.style.backgroundColor='{{ $secondaryColor }}'" onmouseout="this.style.backgroundColor='{{ $primaryColor }}'">
                    {{ $content['pricing_cta'] ?? 'Start Nu Gratis' }}
                </a>
            </div>
        </div>
    </section>

    <!-- Social Proof / Stats Section -->
    <section class="py-12 md:py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 md:mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 md:mb-4 px-2">{{ $content['stats_title'] ?? 'Vertrouwd door Duizenden Klanten' }}</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                    {{ $content['stats_subtitle'] ?? 'Sluit je aan bij tevreden klanten die betrouwbare professionals via ons platform hebben gevonden' }}
                </p>
            </div>

            <!-- Stats -->
            @php
                $stats = $content['stats'] ?? [
                    ['number' => '5.000+', 'label' => 'Tevreden Klanten'],
                    ['number' => '1.200+', 'label' => 'Geverifieerde Professionals'],
                    ['number' => '4.8', 'label' => 'Gemiddelde Beoordeling'],
                    ['number' => '12min', 'label' => 'Gem. Reactietijd'],
                ];
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 md:gap-8 mb-8 md:mb-16">
                @foreach($stats as $stat)
                <div class="text-center bg-white rounded-2xl p-4 sm:p-5 md:p-6 shadow-lg">
                    <div class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-1 md:mb-2" style="color: {{ $primaryColor }};">{{ $stat['number'] ?? '' }}</div>
                    <div class="text-xs sm:text-sm text-gray-600">{{ $stat['label'] ?? '' }}</div>
                </div>
                @endforeach
            </div>

            <!-- Testimonial -->
            @php
                $testimonial = $content['testimonial'] ?? [
                    'quote' => 'Fantastische service! Ik vond binnen 10 minuten een professional en hij repareerde ons probleem dezelfde dag. Het platform maakt alles zo gemakkelijk.',
                    'author' => 'Jan V.',
                    'location' => 'Antwerpen • Professionele Dienst',
                ];
            @endphp
            <div class="relative max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-200">
                    <div class="flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-star text-yellow-400 text-lg md:text-xl"></i>
                        <i class="fas fa-star text-yellow-400 text-lg md:text-xl"></i>
                        <i class="fas fa-star text-yellow-400 text-lg md:text-xl"></i>
                        <i class="fas fa-star text-yellow-400 text-lg md:text-xl"></i>
                        <i class="fas fa-star text-yellow-400 text-lg md:text-xl"></i>
                    </div>

                    <blockquote class="text-base sm:text-lg md:text-xl lg:text-2xl text-gray-900 text-center mb-4 md:mb-6 leading-relaxed px-2">
                        "{{ $testimonial['quote'] ?? '' }}"
                    </blockquote>

                    <div class="text-center">
                        <div class="font-semibold text-gray-900 text-base md:text-lg">{{ $testimonial['author'] ?? '' }}</div>
                        <div class="text-xs sm:text-sm text-gray-600">{{ $testimonial['location'] ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-12 md:py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 md:mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-900 mb-3 md:mb-4 px-2">{{ $content['faq_title'] ?? 'Veelgestelde Vragen' }}</h2>
                <p class="text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto px-2">
                    {{ $content['faq_subtitle'] ?? 'Alles wat je moet weten over ons platform' }}
                </p>
            </div>

            @php
                $faqs = $content['faqs'] ?? [
                    [
                        'question' => 'Hoe werkt het platform?',
                        'answer' => 'Registreer gratis, kies uw diensten, en maak direct contact met geverifieerde professionals in uw buurt via WhatsApp of het platform.'
                    ],
                    [
                        'question' => 'Zijn alle professionals geverifieerd?',
                        'answer' => 'Ja, alle professionals op ons platform ondergaan een grondig verificatieproces inclusief licentiecontrole, verzekering en achtergrondscreening.'
                    ],
                    [
                        'question' => 'Wat zijn de kosten?',
                        'answer' => 'Tijdens onze bètafase zijn alle functies volledig beschikbaar zonder kosten. We informeren alle gebruikers voordat we betaalde abonnementen introduceren.'
                    ],
                    [
                        'question' => 'Hoe snel krijg ik reactie?',
                        'answer' => 'De meeste verzoeken worden binnen 15 minuten gekoppeld aan beschikbare professionals. Voor nooddiensten is de reactietijd vaak nog korter.'
                    ],
                ];
            @endphp
            <div class="space-y-3 md:space-y-4">
                @foreach($faqs as $index => $faq)
                <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <button class="w-full px-4 sm:px-6 py-4 sm:py-5 flex justify-between items-center text-left focus:outline-none" onclick="toggleFaq({{ $index }})">
                        <span class="text-base sm:text-lg font-semibold text-gray-900 pr-2">{{ $faq['question'] ?? '' }}</span>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 flex-shrink-0" id="faq-icon-{{ $index }}"></i>
                    </button>
                    <div class="faq-answer hidden px-4 sm:px-6 pb-4 sm:pb-5">
                        <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ $faq['answer'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 md:py-20 text-white" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 md:mb-6 px-2">{{ $content['cta_title'] ?? 'Klaar om te Beginnen?' }}</h2>
            <p class="text-base sm:text-lg md:text-xl mb-6 md:mb-8 max-w-2xl mx-auto px-2" style="color: rgba(255, 255, 255, 0.9);">
                {{ $content['cta_subtitle'] ?? 'Sluit u aan bij duizenden tevreden klanten en ontdek het gemak van ' . $brandName . '.pro' }}
            </p>
            <a href="{{ route('register', $cat ? ['category' => $cat->code] : []) }}" class="inline-block bg-white px-6 sm:px-8 md:px-10 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-bold hover:bg-gray-100 transition shadow-2xl transform hover:scale-105" style="color: {{ $primaryColor }};">
                <i class="fas fa-rocket mr-2"></i>{{ $content['cta_button'] ?? 'Gratis Account Aanmaken' }}
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 mb-6 md:mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center footer-logo-container" style="background-color: {{ $primaryColor }};">
                            @if($logo)
                                <img src="{{ $logo }}" alt="{{ $brandName }}" class="w-10 h-10 rounded-lg object-cover footer-logo-image" onerror="this.style.display='none'; this.parentElement.querySelector('.footer-logo-icon-fallback').style.display='flex';">
                                <i class="fas {{ $mainIcon }} text-white footer-logo-icon-fallback" style="display: none;"></i>
                            @else
                                <i class="fas {{ $mainIcon }} text-white"></i>
                            @endif
                        </div>
                        <span class="text-xl font-bold">{{ $brandName }}<span style="color: {{ $primaryColor }};">.pro</span></span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        {{ $content['footer_description'] ?? ($cat && $cat->site_description ? $cat->site_description : 'Uw platform voor professionele diensten. Eén account, alle diensten.') }}
                    </p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">{{ $content['footer_services_title'] ?? 'Diensten' }}</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @foreach($categories->take(3) as $catItem)
                            <li><a href="{{ route('register', ['category' => $catItem->code]) }}" class="hover:text-white transition">{{ $catItem->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Bedrijf</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#how-it-works" class="hover:text-white transition">Hoe het werkt</a></li>
                        <li><a href="#features" class="hover:text-white transition">Voordelen</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Prijzen</a></li>
                        <li><a href="{{ route('register', $cat ? ['category' => $cat->code] : []) }}" class="hover:text-white transition">Aanmelden</a></li>
                        <li><a href="{{ route('client.register') }}" class="hover:text-white transition">Klant Account</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">{{ $content['footer_contact_title'] ?? 'Contact' }}</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="mailto:{{ $content['footer_email'] ?? 'info@' . ($cat && $cat->domain ? $cat->domain : 'diensten.pro') }}" class="hover:text-white transition">{{ $content['footer_email'] ?? 'info@' . ($cat && $cat->domain ? $cat->domain : 'diensten.pro') }}</a></li>
                        <li class="flex items-center space-x-2">
                            <i class="fab fa-whatsapp"></i>
                            <span>{{ $content['footer_whatsapp'] ?? 'WhatsApp Support' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $brandName }}.pro. {{ $content['footer_copyright'] ?? 'Alle rechten voorbehouden.' }}</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                menu.classList.add('hidden');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', function() {
                const menu = document.getElementById('mobile-menu');
                const icon = document.getElementById('menu-icon');
                menu.classList.add('hidden');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });

        // FAQ Toggle Function
        function toggleFaq(index) {
            const answer = document.querySelectorAll('.faq-answer')[index];
            const icon = document.getElementById('faq-icon-' + index);
            
            if (answer.classList.contains('hidden')) {
                // Close all other FAQs
                document.querySelectorAll('.faq-answer').forEach((item, i) => {
                    if (i !== index) {
                        item.classList.add('hidden');
                        document.getElementById('faq-icon-' + i).classList.remove('rotate-180');
                    }
                });
                
                // Open this FAQ
                answer.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                // Close this FAQ
                answer.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
