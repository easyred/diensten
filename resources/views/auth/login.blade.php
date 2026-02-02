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
    <title>Inloggen - {{ $brandName }}.pro</title>
    <meta name="description" content="Log in op uw {{ $brandName }}.pro account">
    
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
        .gradient-bg {
            background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);
        }
        .btn-primary {
            background-color: {{ $primaryColor }};
        }
        .btn-primary:hover {
            background-color: {{ $secondaryColor }};
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
                        <a href="{{ route('register', $cat ? ['category' => $cat->code] : []) }}" class="hidden sm:inline-block text-gray-700 hover:text-primary-600 px-4 py-2 rounded-lg font-medium transition">Registreren</a>
                        <a href="{{ route('register', $cat ? ['category' => $cat->code] : []) }}" class="bg-primary-600 text-white px-4 sm:px-6 py-2 rounded-lg hover:bg-primary-700 transition shadow-lg font-semibold text-sm sm:text-base" style="background-color: {{ $primaryColor }};" onmouseover="this.style.backgroundColor='{{ $secondaryColor }}'" onmouseout="this.style.backgroundColor='{{ $primaryColor }}'">
                            Gratis Aanmelden
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-primary-600 px-4 py-2 rounded-lg font-medium transition">Dashboard</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $secondaryColor }} 100%);">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $brandName }}" class="w-20 h-20 rounded-2xl object-cover" onerror="this.style.display='none'; this.parentElement.querySelector('.header-logo-icon-fallback').style.display='flex';">
                        <i class="fas {{ $mainIcon }} text-white text-3xl header-logo-icon-fallback" style="display: none;"></i>
                    @else
                        <i class="fas {{ $mainIcon }} text-white text-3xl"></i>
                    @endif
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Inloggen</h1>
                <p class="text-base sm:text-lg text-gray-600">Log in met uw e-mail en wachtwoord</p>
            </div>

            <!-- Alerts -->
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                    Controleer uw e-mail en wachtwoord.
                </div>
            @endif

            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-100">
                <form method="POST" action="{{ route('login.store') }}" class="space-y-6" novalidate>
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
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
                                   autofocus 
                                   autocomplete="username"
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition text-sm"
                                   style="--tw-ring-color: {{ $primaryColor }};">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Wachtwoord</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" 
                                   type="password" 
                                   name="password" 
                                   placeholder="••••••••" 
                                   required 
                                   autocomplete="current-password"
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

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input id="remember_me" 
                                   type="checkbox" 
                                   name="remember" 
                                   class="w-4 h-4 rounded border-gray-300 focus:ring-primary-500"
                                   style="accent-color: {{ $primaryColor }};">
                            <span class="ml-2 text-sm text-gray-600">Onthoud mij</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium transition" style="color: {{ $primaryColor }};">
                                Wachtwoord vergeten?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full flex items-center justify-center px-6 py-3 border border-transparent rounded-lg text-white font-semibold shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition text-base"
                            style="background-color: {{ $primaryColor }};" 
                            onmouseover="this.style.backgroundColor='{{ $secondaryColor }}'" 
                            onmouseout="this.style.backgroundColor='{{ $primaryColor }}'">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Inloggen
                    </button>
                </form>

                <!-- Footer Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Geen account? 
                        <a href="{{ route('register', $cat ? ['category' => $cat->code] : []) }}" class="font-semibold transition" style="color: {{ $primaryColor }};">
                            Registreer hier
                        </a>
                    </p>
                </div>
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
    </script>
</body>
</html>
