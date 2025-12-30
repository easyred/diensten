<div class="nav-item">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
        <i class="fas fa-tags"></i>
        <span>Domains</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('admin.whatsapp') }}" class="nav-link {{ request()->routeIs('admin.whatsapp*') ? 'active' : '' }}">
        <i class="fab fa-whatsapp"></i>
        <span>WhatsApp</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('admin.flows.index') }}" class="nav-link {{ request()->routeIs('admin.flows*') ? 'active' : '' }}">
        <i class="fas fa-project-diagram"></i>
        <span>WhatsApp Stroomlijnen</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('admin.service-providers.index') }}" class="nav-link {{ request()->routeIs('admin.service-providers*') ? 'active' : '' }}">
        <i class="fas fa-user-tie"></i>
        <span>Service Providers</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('admin.clients.index') }}" class="nav-link {{ request()->routeIs('admin.clients*') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>Klanten</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('admin.requests.index') }}" class="nav-link {{ request()->routeIs('admin.requests*') ? 'active' : '' }}">
        <i class="fas fa-tools"></i>
        <span>Serviceverzoeken</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('admin.tele.index') }}" class="nav-link {{ request()->routeIs('admin.tele*') ? 'active' : '' }}">
        <i class="fas fa-phone"></i>
        <span>Tele Records</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
        <i class="fas fa-user-friends"></i>
        <span>Gebruikers</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('admin.subscriptions') }}" class="nav-link {{ request()->routeIs('admin.subscriptions*') ? 'active' : '' }}">
        <i class="fas fa-credit-card"></i>
        <span>Abonnementen</span>
    </a>
</div>
