<!-- resources/views/partials/sidebar.blade.php -->
@php
$role = session('user.role');
@endphp
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
            Presensi<span>UI</span>
        </a>
        <div class="sidebar-toggler not-active">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item nav-category">Settings</li>

            <li class="nav-item">
                <a href="{{ url('/attendance') }}" class="nav-link">
                    <i class="link-icon" data-feather="calendar"></i>
                    <span class="link-title">Menu Settings</span>
                </a>
            </li>
            
        </ul>
    </div>
</nav>
