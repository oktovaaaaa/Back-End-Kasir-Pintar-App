{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard Admin - Kasir Resto')
@section('page-title', 'Dashboard Admin')

@section('content')
    @php
        $primaryBlue = '#57A0D3';
    @endphp

    <div class="max-w-6xl mx-auto space-y-8 mt-4">

        {{-- ====== BARIS 1 : HERO + OVERVIEW FINANSIAL ====== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Hero kiri (2 kolom) --}}
            <div class="lg:col-span-2">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#E8F2FF] via-white to-[#D4E7FF] p-6 sm:p-7 shadow-sm border border-[#57A0D3]/15">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Hi, {{ $adminName }}</p>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                                Selamat datang di Dashboard
                            </h2>
                            <p class="mt-2 text-xs sm:text-sm text-slate-600 max-w-md">
                                Pantau stok produk, transaksi kasir, pelanggan berutang, dan performa usaha
                                resto Anda dari satu tampilan ringkas.
                            </p>
                        </div>

                        <div class="flex flex-col items-end gap-3">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 text-[11px] text-slate-600 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Sistem berjalan normal
                            </div>
                            <div class="rounded-2xl bg-white/90 px-4 py-3 shadow-sm border border-slate-100 w-full sm:w-56">
                                <p class="text-[11px] text-slate-500 font-semibold">Total Omzet (all time)</p>
                                <p class="mt-1 text-xl font-extrabold text-[#57A0D3]">
                                    Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}
                                </p>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    {{ $totalSalesCount }} transaksi tercatat
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="pointer-events-none absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-[#57A0D3]/10"></div>
                </div>
            </div>

            {{-- Kartu kecil finansial kanan --}}
            <div class="space-y-3">
                <div class="rounded-3xl bg-white shadow-sm border border-[#57A0D3]/20 p-4 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                            Profit Estimasi
                        </p>
                        <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center">
                            <i class='bx bx-trending-up text-emerald-500 text-lg'></i>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl font-extrabold text-emerald-600">
                        Rp {{ number_format($totalProfit, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-[11px] text-slate-500">
                        Selisih harga jual dan modal seluruh transaksi.
                    </p>

                    @php
                        $ratioDebt = $totalSalesAmount > 0 ? min(100, round(($totalPiutang / max(1, $totalSalesAmount)) * 100)) : 0;
                    @endphp
                    <div class="mt-3 flex items-center justify-between">
                        <div class="text-[11px] text-slate-500">
                            <p>Piutang berjalan</p>
                            <p class="font-semibold text-red-600">
                                Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="relative w-16 h-16">
                            <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                            <div class="absolute inset-0 rounded-full border-4 border-[#57A0D3]"
                                 style="clip-path: inset({{ 100 - $ratioDebt }}% 0 0 0);"></div>
                            <div class="absolute inset-1 rounded-full bg-white flex items-center justify-center">
                                <span class="text-[10px] font-semibold text-slate-700">{{ $ratioDebt }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== BARIS 2 : SNAPSHOT PRODUK / PELANGGAN / KASIR / KASBON ====== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            {{-- Produk --}}
            <a href="{{ route('admin.products.index') }}"
               class="group rounded-3xl bg-white border border-slate-100 shadow-sm p-4 flex items-start gap-3 hover:shadow-md hover:-translate-y-0.5 transition">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center bg-[#57A0D3]/10 text-[#57A0D3]">
                    <i class='bx bx-cube text-xl'></i>
                </div>
                <div class="flex-1">
                    <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wide">Produk</p>
                    <p class="mt-1 text-xl font-extrabold text-slate-900">{{ $totalProducts }}</p>
                    <p class="mt-1 text-[11px] text-slate-500">
                        Stok total: <span class="font-semibold text-slate-800">{{ $totalStock }} pcs</span>
                    </p>
                    <p class="mt-1 text-[11px] text-amber-600">
                        {{ $lowStockCount }} menipis • {{ $outStockCount }} habis
                    </p>
                </div>
            </a>

            {{-- Pelanggan --}}
            <a href="{{ route('admin.customers.index') }}"
               class="group rounded-3xl bg-white border border-slate-100 shadow-sm p-4 flex items-start gap-3 hover:shadow-md hover:-translate-y-0.5 transition">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center bg-indigo-50 text-indigo-500">
                    <i class='bx bx-user-circle text-xl'></i>
                </div>
                <div class="flex-1">
                    <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wide">Pelanggan</p>
                    <p class="mt-1 text-xl font-extrabold text-slate-900">{{ $totalCustomers }}</p>
                    <p class="mt-1 text-[11px] text-emerald-700">
                        {{ $noDebtCustomers }} tanpa utang
                    </p>
                    <p class="mt-1 text-[11px] text-red-600">
                        {{ $debtCustomers }} berutang • Piutang:
                        <span class="font-semibold">
                            Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                        </span>
                    </p>
                </div>
            </a>

            {{-- Kasir --}}
            <a href="{{ route('admin.kelola-kasir') }}"
               class="group rounded-3xl bg-white border border-slate-100 shadow-sm p-4 flex items-start gap-3 hover:shadow-md hover:-translate-y-0.5 transition">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center bg-sky-50 text-sky-500">
                    <i class='bx bx-id-card text-xl'></i>
                </div>
                <div class="flex-1">
                    <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wide">Kasir</p>
                    <p class="mt-1 text-xl font-extrabold text-slate-900">{{ $totalCashiersApproved }}</p>
                    <p class="mt-1 text-[11px] text-emerald-700">
                        Aktif: {{ $totalCashiersApproved }}
                    </p>
                    <p class="mt-1 text-[11px] text-amber-600">
                        Pending: {{ $totalCashiersPending }} • Ditolak: {{ $totalCashiersRejected }}
                    </p>
                </div>
            </a>

            {{-- Kasbon --}}
            <a href="{{ route('admin.reports.index') }}"
               class="group rounded-3xl bg-white border border-slate-100 shadow-sm p-4 flex items-start gap-3 hover:shadow-md hover:-translate-y-0.5 transition">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center bg-rose-50 text-rose-500">
                    <i class='bx bx-receipt text-xl'></i>
                </div>
                <div class="flex-1">
                    <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wide">Kasbon</p>
                    <p class="mt-1 text-xl font-extrabold text-rose-600">
                        Rp {{ number_format($totalKasbonAmount, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-[11px] text-slate-500">
                        Total nominal transaksi berstatus kasbon.
                    </p>
                    <p class="mt-1 text-[11px] text-rose-600">
                        Piutang berjalan:
                        <span class="font-semibold">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
                    </p>
                </div>
            </a>
        </div>

        {{-- ====== BARIS 3 : CHART OMZET & PROFIT + DONUT STATUS ====== --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Line chart (dibatasi max width & height) --}}
            <div class="xl:col-span-2 rounded-3xl bg-white shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-700 uppercase tracking-wide">
                            Grafik Omzet & Profit
                        </p>
                        <p class="text-[11px] text-slate-400">
                            7 hari terakhir • lihat detail di menu Laporan Keuangan.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 text-[11px]">
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#57A0D3]"></span>
                            <span>Penjualan</span>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span>Profit</span>
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    {{-- WRAPPER PENTING: batasi lebar & tinggi chart --}}
                    <div class="w-full max-w-3xl mx-auto">
                        <div class="h-64 md:h-72 lg:h-72">
                            <canvas id="dashboardProfitChart" class="w-full h-full"></canvas>
                        </div>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-400">
                        Jika belum ada transaksi dalam 7 hari terakhir, grafik mungkin tampak kosong.
                    </p>
                </div>
            </div>

            {{-- Donut chart status transaksi --}}
            <div class="rounded-3xl bg-white shadow-sm border border-slate-100 overflow-hidden flex flex-col">
                <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Status Transaksi</p>
                        <p class="text-[11px] text-slate-500">
                            Komposisi transaksi Lunas vs Kasbon.
                        </p>
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col items-center justify-center">
                    <div class="w-40 h-40">
                        <canvas id="dashboardStatusDonut" class="w-full h-full"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 w-full text-[11px]">
                        <div class="rounded-2xl bg-emerald-50 px-3 py-2">
                            <div class="flex items-center gap-1 text-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Lunas</span>
                            </div>
                            <p class="mt-1 text-sm font-bold text-emerald-700">
                                {{ number_format(json_decode($donutStatusDataJson)[0] ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-[10px] text-emerald-700/80">
                                Transaksi selesai.
                            </p>
                        </div>
                        <div class="rounded-2xl bg-amber-50 px-3 py-2">
                            <div class="flex items-center gap-1 text-amber-700">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Kasbon</span>
                            </div>
                            <p class="mt-1 text-sm font-bold text-amber-700">
                                {{ number_format(json_decode($donutStatusDataJson)[1] ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="text-[10px] text-amber-700/80">
                                Masih menyisakan utang.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== BARIS 4 : RINGKASAN PENJUALAN & NAVIGASI CEPAT ====== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Ringkasan penjualan kiri --}}
            <a href="{{ route('admin.sales.index') }}"
               class="lg:col-span-2 rounded-3xl bg-white border border-slate-100 shadow-sm p-5 hover:shadow-md hover:-translate-y-0.5 transition flex flex-col">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] text-slate-500 font-semibold uppercase tracking-wide">
                            Ringkasan Penjualan
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Lihat detail di halaman Riwayat Transaksi.
                        </p>
                    </div>
                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#E8F2FF] text-[11px] text-[#57A0D3] font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#57A0D3]"></span>
                        Realtime
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div class="p-3 rounded-2xl bg-[#57A0D3]/5">
                        <p class="text-[11px] text-slate-500">Total transaksi</p>
                        <p class="mt-1 text-lg font-extrabold text-slate-900">
                            {{ $totalSalesCount }}
                        </p>
                    </div>
                    <div class="p-3 rounded-2xl bg-emerald-50">
                        <p class="text-[11px] text-emerald-700">Total Omzet</p>
                        <p class="mt-1 text-lg font-extrabold text-emerald-700">
                            Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="p-3 rounded-2xl bg-indigo-50">
                        <p class="text-[11px] text-indigo-700">Profit Estimasi</p>
                        <p class="mt-1 text-lg font-extrabold text-indigo-700">
                            Rp {{ number_format($totalProfit, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="p-3 rounded-2xl bg-amber-50">
                        <p class="text-[11px] text-amber-700">Piutang</p>
                        <p class="mt-1 text-lg font-extrabold text-amber-700">
                            Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="mt-5 h-16 rounded-2xl bg-gradient-to-r from-[#E8F2FF] via-white to-emerald-50 flex items-center px-4 text-[11px] text-slate-500">
                    <div class="flex-1 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-[#57A0D3]"></span>
                        Penjualan & kasbon detail tersedia di laporan penuh.
                    </div>
                    <div class="hidden sm:flex items-center gap-1 text-[#57A0D3] font-semibold">
                        Lihat histori →
                    </div>
                </div>
            </a>

            {{-- Navigasi cepat kanan --}}
            <div class="space-y-3">
                <div class="rounded-3xl bg-[#57A0D3] text-white p-4 shadow-md">
                    <p class="text-[11px] uppercase font-semibold opacity-80">Navigasi Cepat</p>
                    <p class="mt-1 text-sm opacity-90">
                        Pergi ke halaman detail untuk mengelola data.
                    </p>
                    <div class="mt-3 space-y-2 text-[12px]">
                        <a href="{{ route('admin.products.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-2xl bg-white/10 hover:bg-white/20 transition">
                            <span>Stok Produk</span>
                            <i class='bx bx-right-arrow-alt text-lg'></i>
                        </a>
                        <a href="{{ route('admin.customers.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-2xl bg-white/10 hover:bg-white/20 transition">
                            <span>Pelanggan & Kasbon</span>
                            <i class='bx bx-right-arrow-alt text-lg'></i>
                        </a>
                        <a href="{{ route('admin.reports.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-2xl bg-white/10 hover:bg-white/20 transition">
                            <span>Laporan Keuangan</span>
                            <i class='bx bx-right-arrow-alt text-lg'></i>
                        </a>
                        <a href="{{ route('admin.sales.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-2xl bg-white/10 hover:bg-white/20 transition">
                            <span>Riwayat Transaksi</span>
                            <i class='bx bx-right-arrow-alt text-lg'></i>
                        </a>
                        <a href="{{ route('admin.kelola-kasir') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-2xl bg-white/10 hover:bg-white/20 transition">
                            <span>Kelola Kasir</span>
                            <i class='bx bx-right-arrow-alt text-lg'></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const primaryBlue = '#57A0D3';
            const profitGreen = '#16A34A';

            const labels      = {!! $chartLabelsJson !!};
            const salesData   = {!! $chartSalesJson !!};
            const profitData  = {!! $chartProfitJson !!};

            const statusLabels = {!! $donutStatusLabelsJson !!};
            const statusData   = {!! $donutStatusDataJson !!};

            const currencyFormatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0,
            });

            // LINE CHART OMZET & PROFIT
            const profitCanvas = document.getElementById('dashboardProfitChart');
            if (profitCanvas) {
                const ctx = profitCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Penjualan',
                                data: salesData,
                                borderColor: primaryBlue,
                                backgroundColor: primaryBlue + '33',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 2,
                            },
                            {
                                label: 'Profit',
                                data: profitData,
                                borderColor: profitGreen,
                                backgroundColor: profitGreen + '20',
                                fill: false,
                                tension: 0.4,
                                pointRadius: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // IKUT tinggi wrapper (h-64 / h-72)
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.dataset.label + ': ' + currencyFormatter.format(ctx.parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => currencyFormatter.format(value).replace('Rp', 'Rp ')
                                }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // DONUT CHART STATUS TRANSAKSI
            const donutCanvas = document.getElementById('dashboardStatusDonut');
            if (donutCanvas) {
                const dctx = donutCanvas.getContext('2d');
                new Chart(dctx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusData,
                            backgroundColor: ['#10B981', '#F59E0B'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
@endpush
