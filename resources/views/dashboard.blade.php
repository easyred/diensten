<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - diensten.pro</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #525f7f;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .dashboard-header {
            background: white;
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, 0.15);
        }
        
        .dashboard-card {
            background: white;
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 0 2rem 0 rgba(136, 152, 170, 0.15);
        }
        
        .user-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(87deg, #344767 0, #1a1a1a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin-right: 1rem;
        }
        
        .nav-link {
            color: #525f7f;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            background-color: #f8f9fa;
            color: #344767;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('welcome') }}">
                <i class="fas fa-cog me-2"></i>diensten.pro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    @if(Auth::user()->role === 'super_admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Panel</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link text-danger p-0">
                                <i class="fas fa-sign-out-alt me-1"></i>Uitloggen
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="dashboard-container">
        <!-- Welcome Header -->
        <div class="dashboard-header">
            <div class="d-flex align-items-center">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->full_name ?? Auth::user()->email, 0, 1)) }}
                </div>
                <div>
                    <h2 class="mb-1">Welkom, {{ Auth::user()->full_name ?? Auth::user()->email }}!</h2>
                    <p class="text-muted mb-0">
                        <span class="badge bg-primary">{{ ucfirst(Auth::user()->role ?? 'User') }}</span>
                        @php
                            $userCategories = Auth::user()->categories ?? collect();
                        @endphp
                        @if($userCategories->count() > 0)
                            <span class="ms-2">
                                @foreach($userCategories as $category)
                                    <span class="badge bg-info">{{ $category->name }}</span>
                                @endforeach
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h5 class="mb-3">
                        <i class="fas fa-user-circle text-primary me-2"></i>
                        Account Informatie
                    </h5>
                    <div class="mb-2">
                        <strong>Naam:</strong> {{ Auth::user()->full_name }}
                    </div>
                    <div class="mb-2">
                        <strong>Email:</strong> {{ Auth::user()->email }}
                    </div>
                    @if(Auth::user()->whatsapp_number)
                        <div class="mb-2">
                            <strong>WhatsApp:</strong> {{ Auth::user()->whatsapp_number }}
                        </div>
                    @endif
                    @if(Auth::user()->company_name)
                        <div class="mb-2">
                            <strong>Bedrijf:</strong> {{ Auth::user()->company_name }}
                        </div>
                    @endif
                    @if(Auth::user()->address)
                        <div class="mb-2">
                            <strong>Adres:</strong> {{ Auth::user()->address }}, {{ Auth::user()->postal_code }} {{ Auth::user()->city }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="dashboard-card">
                    <h5 class="mb-3">
                        <i class="fas fa-credit-card text-success me-2"></i>
                        Abonnement Status
                    </h5>
                    @if(Auth::user()->hasActiveSubscription())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Je hebt een actief abonnement!
                        </div>
                        @php
                            $activeSubscription = Auth::user()->subscriptions()->where('status', 'active')->where('ends_at', '>', now())->first();
                        @endphp
                        @if($activeSubscription)
                            <div class="mb-2">
                                <strong>Plan:</strong> {{ $activeSubscription->plan_name }}
                            </div>
                            <div class="mb-2">
                                <strong>Verloopt op:</strong> {{ $activeSubscription->ends_at->format('d-m-Y') }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Geen actief abonnement
                        </div>
                        <a href="{{ route('welcome') }}#pricing" class="btn btn-primary btn-sm">
                            Bekijk Abonnementen
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="dashboard-card">
                    <h5 class="mb-3">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Snelle Acties
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('welcome') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Homepage
                        </a>
                        @if(Auth::user()->role === 'super_admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-success">
                                <i class="fas fa-cog me-2"></i>Admin Panel
                            </a>
                        @endif
                        @if(Auth::user()->role === 'client')
                            <a href="{{ route('client.register') }}" class="btn btn-outline-info">
                                <i class="fas fa-user-plus me-2"></i>Account Beheren
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

