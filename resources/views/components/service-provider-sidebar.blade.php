{{-- Service Provider Sidebar Navigation --}}
<div class="nav-item">
    <a href="{{ route('service-provider.dashboard') }}" class="nav-link {{ request()->routeIs('service-provider.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('service-provider.coverage.index') }}" class="nav-link {{ request()->routeIs('service-provider.coverage.*') ? 'active' : '' }}">
        <i class="fas fa-map-marker-alt"></i>
        <span>WerkZone</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('service-provider.schedule.index') }}" class="nav-link {{ request()->routeIs('service-provider.schedule.*') ? 'active' : '' }}">
        <i class="fas fa-clock"></i>
        <span>Opening hours</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('service-provider.categories.edit') }}" class="nav-link {{ request()->routeIs('service-provider.categories.*') ? 'active' : '' }}">
        <i class="fas fa-tools"></i>
        <span>Categories</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('support') }}" class="nav-link {{ request()->routeIs('support') ? 'active' : '' }}">
        <i class="fas fa-headset"></i>
        <span>Support</span>
    </a>
</div>

<div class="nav-item">
    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="fas fa-user-cog"></i>
        <span>Profiel</span>
    </a>
</div>

