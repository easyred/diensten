 
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - diensten.pro</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --green-accent: #10b981;
            --green-hover: #059669;
            --header-bg-light: #ffffff;
            --header-bg-dark: #1e293b;
            --bg-light: #f8f9fa;
            --bg-dark: #282c34;
            --card-bg-light: #ffffff;
            --card-bg-dark: #3a3f47;
            --text-primary-light: #1f2937;
            --text-primary-dark: #ffffff;
            --text-secondary-light: #6b7280;
            --text-secondary-dark: #9ca3af;
            --border-light: #e5e7eb;
            --border-dark: #4b5563;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-primary-light);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        body.dark {
            background-color: var(--bg-dark);
            color: var(--text-primary-dark);
        }

        /* Top Header */
        .top-header {
            background: linear-gradient(135deg, var(--header-bg-light) 0%, rgba(255, 255, 255, 0.98) 100%);
            border-bottom: 1px solid var(--border-light);
            padding: 0 2rem;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 0 1px 0 rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(20px) saturate(180%);
            transition: all 0.3s ease;
            flex-wrap: wrap;
            gap: 1rem;
        }

        body.dark .top-header {
            background: linear-gradient(135deg, var(--header-bg-dark) 0%, rgba(30, 41, 59, 0.98) 100%);
            border-bottom-color: var(--border-dark);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5), 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        @media (max-width: 1400px) {
            .header-nav {
                max-width: calc(100% - 350px);
            }
        }

        @media (max-width: 1200px) {
            .top-header {
                padding: 0 1.5rem;
            }
            .header-nav {
                margin-left: 1rem;
                gap: 0.25rem;
                max-width: calc(100% - 300px);
            }
            .header-nav a {
                padding: 0.5rem 0.875rem;
                font-size: 0.8125rem;
            }
            .user-greeting {
                font-size: 0.8125rem;
                padding: 0.375rem 0.75rem;
            }
            .status-indicator {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }
        }

        @media (max-width: 992px) {
            .header-nav {
                max-width: calc(100% - 250px);
            }
            .header-nav a {
                padding: 0.5rem 0.75rem;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .top-header {
                padding: 0.75rem 1rem;
                min-height: auto;
            }
            .header-left {
                width: 100%;
                justify-content: space-between;
                margin-bottom: 0.5rem;
            }
            .header-nav {
                order: 3;
                width: 100%;
                margin: 0;
                padding: 0.5rem;
                overflow-x: auto;
                max-width: 100%;
                -webkit-overflow-scrolling: touch;
            }
            .header-nav::-webkit-scrollbar {
                height: 4px;
            }
            .header-right {
                width: 100%;
                justify-content: space-between;
                gap: 0.5rem;
            }
            .user-greeting {
                display: none;
            }
            .status-indicator span:last-child {
                display: none;
            }
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
            min-width: 0;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary-light);
        }

        body.dark .logo-container {
            color: var(--text-primary-dark);
        }

        .logo-square {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--green-accent) 0%, #059669 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.375rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transition: all 0.3s ease;
        }

        .logo-container:hover .logo-square {
            transform: rotate(-5deg) scale(1.05);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .logo-text {
            font-weight: 700;
            font-size: 1.375rem;
            letter-spacing: -0.02em;
        }

        .logo-text .app {
            font-weight: 500;
            font-size: 0.9375rem;
            opacity: 0.7;
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: 2rem;
            padding: 0.5rem 1rem;
            background-color: transparent;
            border: none;
            border-radius: 0;
            flex-wrap: nowrap;
            max-width: calc(100% - 400px);
            min-height: 44px;
        }

        body.dark .header-nav {
            background-color: transparent;
            border: none;
        }

        .header-nav a {
            color: var(--text-secondary-light);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.625rem 1rem;
            border-radius: 8px;
            transition: color 0.2s ease, background-color 0.2s ease;
            position: relative;
            white-space: nowrap;
            flex-shrink: 0;
        }

        body.dark .header-nav a {
            color: var(--text-secondary-dark);
        }

        .header-nav a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--green-accent), #34d399);
            border-radius: 2px;
            transition: width 0.2s ease;
        }

        .header-nav a:hover {
            color: var(--text-primary-light);
            background-color: transparent;
        }

        body.dark .header-nav a:hover {
            color: var(--text-primary-dark);
            background-color: transparent;
        }

        .header-nav a:hover::before {
            width: 80%;
        }

        .header-nav a.active {
            color: var(--text-primary-light);
            font-weight: 600;
            background: transparent;
            box-shadow: none;
        }

        .header-nav a.active::before {
            width: 80%;
            background: var(--green-accent);
        }

        body.dark .header-nav a.active {
            color: var(--text-primary-dark);
            background: transparent;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .header-right-separator {
            width: 1px;
            height: 24px;
            background-color: var(--border-light);
            margin: 0 0.5rem;
        }

        body.dark .header-right-separator {
            background-color: var(--border-dark);
        }

        .header-icon {
            color: var(--text-secondary-light);
            font-size: 1.125rem;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background-color: transparent;
        }

        body.dark .header-icon {
            color: var(--text-secondary-dark);
        }

        .header-icon:hover {
            color: var(--green-accent);
            background-color: rgba(16, 185, 129, 0.1);
            transform: scale(1.1);
        }

        .user-greeting {
            color: var(--text-primary-light);
            font-weight: 600;
            font-size: 0.875rem;
            white-space: nowrap;
            padding: 0.5rem 1rem;
            background-color: rgba(16, 185, 129, 0.06);
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        body.dark .user-greeting {
            color: var(--text-primary-dark);
            background-color: rgba(16, 185, 129, 0.1);
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            color: var(--text-primary-light);
            font-size: 0.8125rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0.06) 100%);
            border-radius: 10px;
            white-space: nowrap;
            border: 1px solid rgba(16, 185, 129, 0.2);
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.1);
        }

        body.dark .status-indicator {
            color: var(--text-primary-dark);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.1) 100%);
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green-accent) 0%, #34d399 100%);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2), 0 2px 4px rgba(16, 185, 129, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2), 0 2px 4px rgba(16, 185, 129, 0.3);
            }
            50% {
                box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.1), 0 2px 4px rgba(16, 185, 129, 0.3);
            }
        }

        /* Main Content */
        .main-content-wrapper {
            min-height: calc(100vh - 80px);
            padding: 2.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Cards */
        .dashboard-card {
            background-color: var(--card-bg-light);
            border-radius: 16px;
            border: 1px solid var(--border-light);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        body.dark .dashboard-card {
            background-color: var(--card-bg-dark);
            border-color: var(--border-dark);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .dashboard-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        body.dark .dashboard-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        }

        .dashboard-card-header {
            padding: 1.75rem 2rem;
            border-bottom: 1px solid var(--border-light);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.04) 0%, rgba(16, 185, 129, 0.01) 100%);
        }

        body.dark .dashboard-card-header {
            border-bottom-color: var(--border-dark);
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.03) 100%);
        }

        .dashboard-card-title {
            font-size: 1.375rem;
            font-weight: 700;
            color: var(--text-primary-light);
            margin: 0;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dashboard-card-title i {
            color: var(--green-accent);
            font-size: 1.25rem;
        }

        body.dark .dashboard-card-title {
            color: var(--text-primary-dark);
        }

        .dashboard-card-body {
            padding: 2rem;
        }

        /* Alerts */
        .alert {
            border-radius: 8px;
            border: 1px solid;
        }

        body.dark .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
            color: #86efac;
        }

        body.dark .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        /* Text Colors */
        body.dark .text-muted {
            color: var(--text-secondary-dark) !important;
        }

        body.dark h1, body.dark h2, body.dark h3, body.dark h4, body.dark h5, body.dark h6 {
            color: var(--text-primary-dark) !important;
        }

        body.dark p {
            color: var(--text-primary-dark);
        }

        /* Cards - Bootstrap compatibility */
        .card {
            background-color: var(--card-bg-light);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        body.dark .card {
            background-color: var(--card-bg-dark);
            border-color: var(--border-dark);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        body.dark .card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border-light);
            padding: 1.5rem 1.75rem;
            color: var(--text-primary-light);
        }

        body.dark .card-header {
            border-bottom-color: var(--border-dark);
            color: var(--text-primary-dark);
        }

        .card-body {
            padding: 1.75rem;
            color: var(--text-primary-light);
        }

        body.dark .card-body {
            color: var(--text-primary-dark);
        }

        /* Tables */
        .table {
            color: var(--text-primary-light);
            background-color: var(--card-bg-light);
        }

        body.dark .table {
            color: var(--text-primary-dark);
            background-color: var(--card-bg-dark) !important;
            --bs-table-bg: var(--card-bg-dark) !important;
            --bs-table-color: var(--text-primary-dark) !important;
            --bs-table-border-color: var(--border-dark) !important;
        }

        body.dark .table thead {
            background-color: var(--card-bg-dark) !important;
        }

        body.dark .table thead th {
            background-color: var(--card-bg-dark) !important;
            color: var(--text-primary-dark) !important;
            border-color: var(--border-dark) !important;
        }

        body.dark .table tbody {
            background-color: var(--card-bg-dark) !important;
        }

        body.dark .table tbody tr {
            background-color: var(--card-bg-dark) !important;
            border-color: var(--border-dark) !important;
            color: var(--text-primary-dark) !important;
        }

        body.dark .table tbody tr:hover {
            background-color: #4b5563 !important;
            color: var(--text-primary-dark) !important;
        }

        body.dark .table tbody td {
            background-color: var(--card-bg-dark) !important;
            border-color: var(--border-dark) !important;
            color: var(--text-primary-dark) !important;
        }

        body.dark .table tbody tr:hover td {
            background-color: #4b5563 !important;
            color: var(--text-primary-dark) !important;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--green-accent);
            border-color: var(--green-accent);
        }

        .btn-primary:hover {
            background-color: var(--green-hover);
            border-color: var(--green-hover);
        }

        .btn-success {
            background-color: var(--green-accent);
            border-color: var(--green-accent);
        }

        .btn-success:hover {
            background-color: var(--green-hover);
            border-color: var(--green-hover);
        }

        /* Form elements */
        .form-control, .form-select {
            background-color: var(--card-bg-light);
            border-color: var(--border-light);
            color: var(--text-primary-light);
        }

        body.dark .form-control, body.dark .form-select {
            background-color: var(--card-bg-dark);
            border-color: var(--border-dark);
            color: var(--text-primary-dark);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--card-bg-light);
            border-color: var(--green-accent);
            color: var(--text-primary-light);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        body.dark .form-control:focus, body.dark .form-select:focus {
            background-color: var(--card-bg-dark);
            border-color: var(--green-accent);
            color: var(--text-primary-dark);
        }

        .form-label {
            color: var(--text-primary-light);
        }

        body.dark .form-label {
            color: var(--text-primary-dark);
        }

        /* Badges */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
        }

        /* Links */
        a {
            color: var(--text-primary-light);
            text-decoration: none;
        }

        body.dark a {
            color: var(--text-primary-dark);
        }

        a:hover {
            color: var(--green-accent);
        }

        /* Strong/Bold text */
        strong, .fw-semibold, .fw-bold {
            color: var(--text-primary-light);
        }

        body.dark strong, body.dark .fw-semibold, body.dark .fw-bold {
            color: var(--text-primary-dark);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-header {
                padding: 0 1rem;
                height: auto;
                min-height: 70px;
                flex-wrap: wrap;
            }

            .header-left {
                gap: 1rem;
            }

            .header-nav {
                display: none;
            }

            .header-right {
                gap: 0.75rem;
                flex-wrap: wrap;
            }

            .main-content-wrapper {
                padding: 1rem;
            }

            .user-greeting {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <a href="{{ route('dashboard') }}" class="logo-container">
                <div class="logo-square">
                    <i class="fas fa-wrench"></i>
                </div>
                <div class="logo-text">
                    diensten<span class="app">.pro</span>
                </div>
            </a>
            
            <nav class="header-nav">
                @if(in_array(Auth::user()->role ?? '', ['plumber', 'gardener']))
                    <a href="{{ route('service-provider.dashboard') }}" class="{{ request()->routeIs('service-provider.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('service-provider.coverage.index') }}" class="{{ request()->routeIs('service-provider.coverage.*') ? 'active' : '' }}">WerkZone</a>
                    <a href="{{ route('service-provider.schedule.index') }}" class="{{ request()->routeIs('service-provider.schedule.*') ? 'active' : '' }}">Openingstijden</a>
                    <a href="{{ route('service-provider.categories.edit') }}" class="{{ request()->routeIs('service-provider.categories.*') ? 'active' : '' }}">Categorieën</a>
                    <a href="{{ route('support') }}" class="{{ request()->routeIs('support') ? 'active' : '' }}">Ondersteuning</a>
                @elseif((Auth::user()->role ?? '') === 'client')
                    <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('welcome') }}#pricing" class="">Abonneer</a>
                    <a href="{{ route('support') }}" class="{{ request()->routeIs('support') ? 'active' : '' }}">Ondersteuning</a>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profiel</a>
                @else
                    <a href="{{ route('welcome') }}#how-it-works">Hoe het werkt</a>
                    <a href="{{ route('welcome') }}#services">Diensten</a>
                    <a href="{{ route('welcome') }}#pricing">Prijzen</a>
                    <a href="{{ route('support') }}">Ondersteuning</a>
                @endif
            </nav>
        </div>
        
        <div class="header-right">
            <span class="user-greeting">Hallo, {{ explode(' ', Auth::user()->full_name ?? Auth::user()->email)[0] }}</span>
            
            <div class="header-right-separator"></div>
            
            @if(in_array(Auth::user()->role ?? '', ['plumber', 'gardener']))
            <div class="status-indicator">
                <span class="status-dot"></span>
                <span>Status: {{ ucfirst(Auth::user()->status ?? 'Beschikbaar') }}</span>
            </div>
            @endif
            
            <x-dark-mode-toggle />
            
            <i class="fas fa-sign-out-alt header-icon" onclick="document.getElementById('logout-form').submit();" style="cursor: pointer; margin-left: 0.5rem;" title="Uitloggen"></i>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content-wrapper">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>

