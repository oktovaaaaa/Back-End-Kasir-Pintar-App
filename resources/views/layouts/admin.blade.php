{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Admin Kasir - Kasir Resto')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- CSS sidebar admin --}}
    <link rel="stylesheet" href="{{ asset('css/admin-sidebar.css') }}">

    {{-- Boxicons CDN --}}
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

<div class="sidebar open">
    <div class="logo-details">
        <i class="bx bx-store-alt icon"></i>
        <div class="logo_name">Kasir Resto</div>
        <i class="bx bx-menu" id="btn"></i>
    </div>

    <ul class="nav-list">
        {{-- Search --}}
        <li>
            <i class="bx bx-search"></i>
            <input type="text" placeholder="Search..." />
            <span class="tooltip">Search</span>
        </li>

        {{-- Dashboard --}}
        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <i class="bx bx-grid-alt"></i>
                <span class="links_name">Dashboard</span>
            </a>
            <span class="tooltip">Dashboard</span>
        </li>

        {{-- Kelola Kasir --}}
        <li class="{{ request()->routeIs('admin.kelola-kasir') ? 'active' : '' }}">
            <a href="{{ route('admin.kelola-kasir') }}">
                <i class='bx bx-user-circle'></i>
                <span class="links_name">Kelola Kasir</span>
            </a>
            <span class="tooltip">Kelola Kasir</span>
        </li>

        {{-- Stok Produk --}}
        <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <a href="{{ route('admin.products.index') }}">
                <i class='bx bx-package'></i>
                <span class="links_name">Stok Produk</span>
            </a>
            <span class="tooltip">Stok Produk</span>
        </li>

        {{-- Laporan --}}
        <li class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reports.index') }}">
                <i class='bx bx-bar-chart-alt-2'></i>
                <span class="links_name">Laporan</span>
            </a>
            <span class="tooltip">Laporan</span>
        </li>

        {{-- Riwayat Transaksi --}}
        <li class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
            <a href="{{ route('admin.sales.index') }}">
                <i class='bx bx-receipt'></i>
                <span class="links_name">Riwayat</span>
            </a>
            <span class="tooltip">Riwayat Transaksi</span>
        </li>

        {{-- Pelanggan --}}
        <li class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <a href="{{ route('admin.customers.index') }}">
                <i class='bx bx-user'></i>
                <span class="links_name">Pelanggan</span>
            </a>
            <span class="tooltip">Pelanggan</span>
        </li>

        {{-- Pengaturan --}}
        <li>
            <a href="#">
                <i class="bx bx-cog"></i>
                <span class="links_name">Pengaturan</span>
            </a>
            <span class="tooltip">Pengaturan</span>
        </li>

        {{-- Profile + Logout --}}
        <li class="profile">
            <div class="profile-details">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(optional(auth('admin')->user())->name ?? 'Admin') }}"
                     alt="profileImg" />
                <div class="name_job">
                    <div class="name">{{ optional(auth('admin')->user())->name ?? 'Admin' }}</div>
                    <div class="job">Administrator</div>
                </div>
            </div>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit"
                        style="all:unset;cursor:pointer;width:100%;display:block;"
                        title="Keluar">
                    <i class="bx bx-log-out" id="log_out"></i>
                </button>
            </form>
        </li>
    </ul>
</div>

<section class="home-section">
    <div class="text">
        @yield('page-title', 'Dashboard Admin')
    </div>

    <div style="padding: 0 20px 40px;">
        @yield('content')
    </div>
</section>

<script src="{{ asset('js/admin-sidebar.js') }}"></script>
@stack('scripts')
</body>
</html>
