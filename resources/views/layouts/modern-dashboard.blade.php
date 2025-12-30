 
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
            --primary-color: #344767;
            --secondary-color: #f7fafc;
            --success-color: #2dce89;
            --info-color: #11cdef;
            --warning-color: #fb6340;
            --danger-color: #f5365c;
            --dark-color: #1a1a1a;
            --light-color: #f8f9fa;
            --sidebar-width: 260px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #525f7f;
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Donkere modus stijlen */
        body.dark {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        /* Sidebar Stijlen */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(87deg, #344767 0, #1a1a1a 100%);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand i {
            font-size: 1.5rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 1rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        /* Hoofdinhoud */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Kop */
        .header {
            background: white;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, 0.15);
            position: sticky;
            top: 0;
            z-index: 999;
            transition: background-color 0.3s ease;
        }
        
        body.dark .header {
            background: #1e293b;
            box-shadow: 0 0 2rem 0 rgba(0, 0, 0, 0.3);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: #525f7f;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .sidebar-toggle:hover {
            background-color: #f8f9fa;
        }
        
        body.dark .sidebar-toggle {
            color: #e2e8f0;
        }
        
        body.dark .sidebar-toggle:hover {
            background-color: #334155;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #172b4d;
            margin: 0;
        }
        
        body.dark .page-title {
            color: #f1f5f9;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-menu:hover {
            background: #e9ecef;
        }
        
        body.dark .user-menu {
            background: #334155;
        }
        
        body.dark .user-menu:hover {
            background: #475569;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: #172b4d;
            font-size: 0.875rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: #8898aa;
            text-transform: capitalize;
        }
        
        body.dark .user-name {
            color: #f1f5f9;
        }
        
        body.dark .user-role {
            color: #94a3b8;
        }

        /* Inhoudgebied */
        .content {
            padding: 2rem;
        }

        /* Kaarten */
        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, 0.15);
            border: none;
            margin-bottom: 1.5rem;
            transition: background-color 0.3s ease;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            font-weight: 600;
            color: #172b4d;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .card-body {
            padding: 1.5rem;
        }
        
        body.dark .card {
            background: #1e293b;
            box-shadow: 0 0 2rem 0 rgba(0, 0, 0, 0.3);
        }
        
        body.dark .card-header {
            background: transparent;
            border-bottom: 1px solid #334155;
            color: #f1f5f9;
        }
        
        body.dark .card-body {
            color: #e2e8f0;
            background-color: #1e293b !important;
        }
        
        body.dark .card-body .table {
            background-color: #1e293b !important;
        }
        
        body.dark .text-muted {
            color: #94a3b8 !important;
        }
        
        body.dark .fw-semibold {
            color: #f1f5f9 !important;
        }
        
        body.dark h1, body.dark h2, body.dark h3, body.dark h4, body.dark h5, body.dark h6 {
            color: #f1f5f9 !important;
        }
        
        body.dark p {
            color: #e2e8f0;
        }
        
        body.dark .btn-outline-primary {
            color: #60a5fa;
            border-color: #60a5fa;
        }
        
        body.dark .btn-outline-primary:hover {
            background-color: #60a5fa;
            border-color: #60a5fa;
            color: #1e293b;
        }
        
        body.dark .btn-outline-secondary {
            color: #94a3b8;
            border-color: #94a3b8;
        }
        
        body.dark .btn-outline-secondary:hover {
            background-color: #94a3b8;
            border-color: #94a3b8;
            color: #1e293b;
        }
        
        body.dark .btn-outline-info {
            color: #67e8f9;
            border-color: #67e8f9;
        }
        
        body.dark .btn-outline-info:hover {
            background-color: #67e8f9;
            border-color: #67e8f9;
            color: #1e293b;
        }
        
        body.dark .btn-outline-warning {
            color: #fbbf24;
            border-color: #fbbf24;
        }
        
        body.dark .btn-outline-warning:hover {
            background-color: #fbbf24;
            border-color: #fbbf24;
            color: #1e293b;
        }
        
        body.dark .btn-outline-success {
            color: #34d399;
            border-color: #34d399;
        }
        
        body.dark .btn-outline-success:hover {
            background-color: #34d399;
            border-color: #34d399;
            color: #1e293b;
        }
        
        body.dark .btn-outline-danger {
            color: #f87171;
            border-color: #f87171;
        }
        
        body.dark .btn-outline-danger:hover {
            background-color: #f87171;
            border-color: #f87171;
            color: #1e293b;
        }
        
        body.dark .border {
            border-color: #334155 !important;
        }
        
        body.dark .alert-success {
            background-color: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.2);
            color: #86efac;
        }
        
        body.dark .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }
        
        body.dark .badge {
            color: #1e293b;
        }
        
        body.dark .badge.bg-success {
            background-color: #10b981 !important;
        }
        
        body.dark .badge.bg-warning {
            background-color: #f59e0b !important;
        }
        
        body.dark .badge.bg-info {
            background-color: #3b82f6 !important;
        }
        
        body.dark .badge.bg-danger {
            background-color: #ef4444 !important;
        }
        
        body.dark .badge.bg-secondary {
            background-color: #64748b !important;
        }
        
        body.dark .badge.bg-primary {
            background-color: #3b82f6 !important;
        }

        /* Tabellen - Donkere Modus - Dwing overschrijving van Bootstrap-standaard */
        body.dark .table,
        body.dark table.table {
            color: #e2e8f0 !important;
            border-color: #334155 !important;
            background-color: #1e293b !important;
            --bs-table-bg: #1e293b !important;
            --bs-table-color: #e2e8f0 !important;
            --bs-table-border-color: #334155 !important;
        }
        
        body.dark .table thead {
            background-color: #1e293b !important;
        }
        
        body.dark .table thead th {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
        
        body.dark .table tbody {
            background-color: #1e293b !important;
        }
        
        body.dark .table tbody tr {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        
        body.dark .table tbody tr:hover {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        
        body.dark .table tbody td {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        
        body.dark .table tbody tr:hover td {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        
        body.dark .table-light {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
        }
        
        body.dark .table-light thead th {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
        }
        
        body.dark .table-hover tbody tr:hover {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        
        body.dark .table-responsive {
            background-color: transparent !important;
        }
        
        /* Overschrijf de standaard witte tafel achtergrond in kaarten */
        body.dark .card .table,
        body.dark .card-body .table {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
        }
        
        body.dark .card .table thead,
        body.dark .card-body .table thead {
            background-color: #1e293b !important;
        }
        
        body.dark .card .table thead th,
        body.dark .card-body .table thead th {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
        
        body.dark .card .table tbody,
        body.dark .card-body .table tbody {
            background-color: #1e293b !important;
        }
        
        body.dark .card .table tbody tr,
        body.dark .card-body .table tbody tr {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
        }
        
        body.dark .card .table tbody tr td,
        body.dark .card-body .table tbody tr td {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }
        
        body.dark .card .table tbody tr:hover,
        body.dark .card-body .table tbody tr:hover {
            background-color: #334155 !important;
        }
        
        body.dark .card .table tbody tr:hover td,
        body.dark .card-body .table tbody tr:hover td {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        
        /* Links in Donkere Modus */
        body.dark a {
            color: #60a5fa;
        }
        
        body.dark a:hover {
            color: #93c5fd;
        }
        
        body.dark a.text-decoration-none {
            color: #60a5fa;
        }
        
        body.dark a.text-success {
            color: #34d399 !important;
        }
        
        body.dark a.text-success:hover {
            color: #6ee7b7 !important;
        }
        
        /* Pagina-navigatie - Donkere Modus */
        body.dark .pagination {
            --bs-pagination-color: #60a5fa;
            --bs-pagination-bg: #1e293b;
            --bs-pagination-border-color: #334155;
            --bs-pagination-hover-color: #93c5fd;
            --bs-pagination-hover-bg: #1e293b;
            --bs-pagination-hover-border-color: #475569;
            --bs-pagination-focus-color: #93c5fd;
            --bs-pagination-focus-bg: #1e293b;
            --bs-pagination-active-color: #fff;
            --bs-pagination-active-bg: #3b82f6;
            --bs-pagination-active-border-color: #3b82f6;
            --bs-pagination-disabled-color: #64748b;
            --bs-pagination-disabled-bg: #1e293b;
            --bs-pagination-disabled-border-color: #334155;
        }
        
        body.dark .page-link {
            background-color: #1e293b;
            border-color: #334155;
            color: #60a5fa;
        }
        
        body.dark .page-link:hover {
            background-color: #1e293b;
            border-color: #475569;
            color: #93c5fd;
        }
        
        body.dark .page-item.active .page-link {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
        }
        
        body.dark .page-item.disabled .page-link {
            background-color: #1e293b;
            border-color: #334155;
            color: #64748b;
            cursor: not-allowed;
        }
        
        /* Formulieren - Donkere Modus */
        body.dark .form-control {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        
        body.dark .form-control:focus {
            background-color: #1e293b;
            border-color: #60a5fa;
            color: #e2e8f0;
        }
        
        body.dark .form-select {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        
        body.dark .form-select:focus {
            background-color: #1e293b;
            border-color: #60a5fa;
            color: #e2e8f0;
        }
        
        /* Inhoudgebied - Donkere Modus */
        body.dark .content {
            background-color: #0f172a;
        }
        
        body.dark h4 {
            color: #f1f5f9 !important;
        }
        
        body.dark .fw-medium {
            color: #f1f5f9 !important;
        }
        
        /* Rijen en Kolommen Styling - Donkere Modus */
        body.dark .row {
            color: #e2e8f0;
        }
        
        /* Kleine Tekst - Donkere Modus */
        body.dark .small {
            color: #cbd5e1;
        }
        
        /* Lege Status - Donkere Modus */
        body.dark .text-center {
            color: #e2e8f0;
        }
        
        body.dark .text-center .text-muted {
            color: #94a3b8 !important;
        }
        
        /* Tafel Links - Donkere Modus */
        body.dark .table a {
            color: #60a5fa;
        }
        
        body.dark .table a:hover {
            color: #93c5fd;
        }
        
        body.dark .table a.text-success {
            color: #34d399 !important;
        }
        
        body.dark .table a.text-success:hover {
            color: #6ee7b7 !important;
        }

        /* Statistieken Kaarten */
        .stats-card {
            background: linear-gradient(87deg, var(--primary-color) 0, #1a1a1a 100%);
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stats-card.success {
            background: linear-gradient(87deg, var(--success-color) 0, #2dcecc 100%);
        }

        .stats-card.info {
            background: linear-gradient(87deg, var(--info-color) 0, #1171ef 100%);
        }

        .stats-card.warning {
            background: linear-gradient(87deg, var(--warning-color) 0, #fbb140 100%);
        }

        .stats-card.danger {
            background: linear-gradient(87deg, var(--danger-color) 0, #f56036 100%);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stats-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        /* Knoppen */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: #4a56d4;
            border-color: #4a56d4;
        }

        /* Responsief */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content {
                padding: 1rem;
            }
        }

        /* Hulpmiddelen */
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: var(--success-color) !important; }
        .text-info { color: var(--info-color) !important; }
        .text-warning { color: var(--warning-color) !important; }
        .text-danger { color: var(--danger-color) !important; }
        .text-dark { color: var(--dark-color) !important; }

        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-success { background-color: var(--success-color) !important; }
        .bg-info { background-color: var(--info-color) !important; }
        .bg-warning { background-color: var(--warning-color) !important; }
        .bg-danger { background-color: var(--danger-color) !important; }
    </style>
    
    <!-- Extra Donkere Modus Overschrijvingen voor Tabellen -->
    <style>
        /* Dwing donkere tafel achtergronden - moet Bootstrap overschrijven */
        body.dark .card-body table.table,
        body.dark .card table.table,
        body.dark table.table {
            background-color: #1e293b !important;
        }
        
        body.dark .card-body table.table tbody,
        body.dark .card table.table tbody,
        body.dark table.table tbody {
            background-color: #1e293b !important;
        }
        
        body.dark .card-body table.table tbody tr,
        body.dark .card table.table tbody tr,
        body.dark table.table tbody tr {
            background-color: #1e293b !important;
        }
        
        body.dark .card-body table.table tbody tr td,
        body.dark .card table.table tbody tr td,
        body.dark table.table tbody tr td {
            background-color: #1e293b !important;
        }
        
        body.dark .card-body table.table thead,
        body.dark .card table.table thead,
        body.dark table.table thead {
            background-color: #1e293b !important;
        }
        
        body.dark .card-body table.table thead th,
        body.dark .card table.table thead th,
        body.dark table.table thead th {
            background-color: #1e293b !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <i class="fas fa-cog"></i>
                <span>diensten.pro</span>
            </a>
        </div>
        
        <div class="sidebar-nav">
            @yield('sidebar-nav')
        </div>
    </nav>

    <!-- Hoofdinhoud -->
    <div class="main-content" id="main-content">
        <!-- Kop -->
        <header class="header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            
            <div class="header-right">
                <x-dark-mode-toggle />
                
                <div class="user-menu">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->email, 0, 1)) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->full_name ?? Auth::user()->email }}</div>
                        <div class="user-role">{{ Auth::user()->role }}</div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Uitloggen
                    </button>
                </form>
            </div>
        </header>

        <!-- Inhoud -->
        <main class="content">
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
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        // Sidebar toggle functionaliteit
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Sluit sidebar op mobiel wanneer buiten de sidebar wordt geklikt
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target) && 
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });

        // Actieve navigatie highlighten
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
 