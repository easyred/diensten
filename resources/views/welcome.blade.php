<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>diensten.pro - Uw Platform voor Professionele Diensten</title>
    <meta name="description" content="Vind en boek professionele diensten zoals loodgieters, tuinmannen en meer. Eén account, toegang tot alle diensten.">
    
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
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
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
            color: #0284c7;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 24px;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50 backdrop-blur-sm bg-opacity-95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-briefcase text-white text-xl"></i>
                    </div>
                    <div>
                        <a href="{{ route('welcome') }}" class="text-2xl font-bold text-gray-900 hover:text-primary-600 transition">
                            diensten<span class="text-primary-600">.pro</span>
                        </a>
                        <p class="text-xs text-gray-500 -mt-1">Professionele Diensten</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#services" class="nav-link text-gray-700 font-medium">Diensten</a>
                    <a href="#how-it-works" class="nav-link text-gray-700 font-medium">Hoe het werkt</a>
                    <a href="#features" class="nav-link text-gray-700 font-medium">Voordelen</a>
                    <a href="#pricing" class="nav-link text-gray-700 font-medium">Prijzen</a>
                </div>
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-600 px-4 py-2 rounded-lg font-medium transition">Inloggen</a>
                        <a href="{{ route('register') }}" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition shadow-lg font-semibold">
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
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg text-white py-24 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <h1 class="text-6xl font-bold mb-6 leading-tight">
                    Vind de Perfecte<br>
                    <span class="text-yellow-300">Professionele Dienst</span>
                </h1>
                <p class="text-xl text-gray-100 mb-10 max-w-3xl mx-auto leading-relaxed">
                    Eén platform voor alle professionele diensten. Van loodgieters tot tuinmannen, 
                    vind en boek betrouwbare professionals in uw buurt. Eén account, toegang tot alle diensten.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="{{ route('register') }}" class="bg-white text-primary-600 px-8 py-4 rounded-lg text-lg font-bold hover:bg-gray-100 transition shadow-2xl transform hover:scale-105">
                        <i class="fas fa-rocket mr-2"></i>Start Nu Gratis
                    </a>
                    <a href="{{ route('client.register') }}" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-white hover:text-primary-600 transition">
                        <i class="fas fa-user mr-2"></i>Klant Account
                    </a>
                    <a href="#services" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-white hover:text-primary-600 transition">
                        <i class="fas fa-search mr-2"></i>Bekijk Diensten
                    </a>
                </div>
                <div class="mt-12 flex justify-center items-center space-x-8 text-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-check-circle text-yellow-300"></i>
                        <span>Gratis Aanmelden</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-check-circle text-yellow-300"></i>
                        <span>Geen Verborgen Kosten</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-check-circle text-yellow-300"></i>
                        <span>24/7 Beschikbaar</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Onze Diensten</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Kies uit ons uitgebreide aanbod van professionele dienstverleners. 
                    Elke dienst wordt geleverd door geverifieerde professionals.
                </p>
            </div>
            
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($categories as $category)
                        <div class="service-card bg-white rounded-2xl shadow-xl p-8 border border-gray-100 hover:border-primary-200">
                            <div class="text-center">
                                @if($category->logo_url)
                                    <img src="{{ $category->logo_url }}" alt="{{ $category->name }}" class="w-24 h-24 mx-auto mb-6 rounded-2xl object-cover shadow-lg">
                                @else
                                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center shadow-lg">
                                        @if($category->code === 'plumber')
                                            <i class="fas fa-wrench text-white text-4xl"></i>
                                        @elseif($category->code === 'gardener')
                                            <i class="fas fa-leaf text-white text-4xl"></i>
                                        @else
                                            <i class="fas fa-tools text-white text-4xl"></i>
                                        @endif
                                    </div>
                                @endif
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ $category->name }}</h3>
                                <p class="text-gray-600 mb-6 leading-relaxed">
                                    Professionele {{ strtolower($category->name) }} diensten van geverifieerde experts in uw regio.
                                </p>
                                <div class="space-y-3">
                                    <a href="{{ route('register', ['category' => $category->code]) }}" class="block w-full bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition font-semibold shadow-lg">
                                        <i class="fas fa-user-plus mr-2"></i>Aanmelden voor {{ $category->name }}
                                    </a>
                                    @if($category->domain)
                                        <a href="http://{{ $category->domain }}" target="_blank" class="block w-full bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                                            <i class="fas fa-external-link-alt mr-2"></i>Bezoek {{ $category->name }} Website
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <i class="fas fa-info-circle text-gray-400 text-6xl mb-4"></i>
                    <p class="text-gray-600 text-xl">Er zijn nog geen diensten beschikbaar. Binnenkort meer!</p>
                </div>
            @endif
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Hoe Het Werkt</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    In drie eenvoudige stappen vindt u de perfecte professional voor uw behoeften.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg">
                        1
                    </div>
                    <div class="mt-6 mb-6">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Maak een Account</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Registreer gratis en kies uw gewenste diensten. Eén account geeft toegang tot alle platforms.
                    </p>
                    <a href="{{ route('client.register') }}" class="text-primary-600 hover:text-primary-700 font-semibold text-sm">
                        <i class="fas fa-user mr-1"></i>Of maak een klant account
                    </a>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg">
                        2
                    </div>
                    <div class="mt-6 mb-6">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Zoek Professionals</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Blader door geverifieerde professionals in uw buurt en bekijk hun profielen en beoordelingen.
                    </p>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center relative">
                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 w-12 h-12 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg">
                        3
                    </div>
                    <div class="mt-6 mb-6">
                        <div class="feature-icon mx-auto">
                            <i class="fas fa-handshake"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Boek & Geniet</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Boek direct via WhatsApp of het platform. Ontvang updates en geniet van professionele service.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Waarom diensten.pro?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Het platform dat alles samenbrengt voor uw gemak en vertrouwen.
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 text-center border border-blue-200">
                    <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Veilig & Betrouwbaar</h3>
                    <p class="text-gray-600 text-sm">Uw gegevens zijn beschermd met enterprise-grade beveiliging</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 text-center border border-green-200">
                    <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Universele Toegang</h3>
                    <p class="text-gray-600 text-sm">Eén account werkt op alle service platforms</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8 text-center border border-purple-200">
                    <div class="w-16 h-16 bg-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-credit-card text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Flexibele Abonnementen</h3>
                    <p class="text-gray-600 text-sm">Eén abonnement, toegang tot alle diensten</p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-8 text-center border border-orange-200">
                    <div class="w-16 h-16 bg-orange-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">24/7 Ondersteuning</h3>
                    <p class="text-gray-600 text-sm">Altijd hulp wanneer u het nodig heeft</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Eenvoudige Prijzen</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Kies het abonnement dat bij u past. Eén betaling, toegang tot alle diensten.
                </p>
            </div>
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-2xl p-12 text-center">
                <div class="mb-8">
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">Standaard Abonnement</h3>
                    <div class="flex items-baseline justify-center mb-6">
                        <span class="text-6xl font-bold text-primary-600">€29</span>
                        <span class="text-2xl text-gray-500 ml-2">/maand</span>
                    </div>
                    <p class="text-gray-600 text-lg mb-8">
                        Volledige toegang tot alle diensten en platforms
                    </p>
                </div>
                <div class="space-y-4 mb-8 text-left max-w-md mx-auto">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Toegang tot alle service platforms</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Onbeperkt verzoeken indienen</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Prioriteit ondersteuning</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Geen verborgen kosten</span>
                    </div>
                </div>
                <a href="{{ route('register') }}" class="inline-block bg-primary-600 text-white px-10 py-4 rounded-lg text-lg font-bold hover:bg-primary-700 transition shadow-lg transform hover:scale-105">
                    Start Nu Gratis
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold mb-6">Klaar om te Beginnen?</h2>
            <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                Sluit u aan bij duizenden tevreden klanten en ontdek het gemak van diensten.pro
            </p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-primary-600 px-10 py-4 rounded-lg text-lg font-bold hover:bg-gray-100 transition shadow-2xl transform hover:scale-105">
                <i class="fas fa-rocket mr-2"></i>Gratis Account Aanmaken
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-briefcase text-white"></i>
                        </div>
                        <span class="text-xl font-bold">diensten<span class="text-primary-400">.pro</span></span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Uw platform voor professionele diensten. Eén account, alle diensten.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Diensten</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @foreach($categories->take(3) as $category)
                            <li><a href="{{ route('register', ['category' => $category->code]) }}" class="hover:text-white transition">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Bedrijf</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#how-it-works" class="hover:text-white transition">Hoe het werkt</a></li>
                        <li><a href="#features" class="hover:text-white transition">Voordelen</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Prijzen</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Aanmelden</a></li>
                        <li><a href="{{ route('client.register') }}" class="hover:text-white transition">Klant Account</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="mailto:info@diensten.pro" class="hover:text-white transition">info@diensten.pro</a></li>
                        <li class="flex items-center space-x-2">
                            <i class="fab fa-whatsapp"></i>
                            <span>WhatsApp Support</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} diensten.pro. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>

    <script>
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
