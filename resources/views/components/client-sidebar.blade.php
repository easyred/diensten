{{-- Client Sidebar Navigation --}}
<div class="nav-item">
    <a href="{{ route('client.dashboard') }}" class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dashboard</span>
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

