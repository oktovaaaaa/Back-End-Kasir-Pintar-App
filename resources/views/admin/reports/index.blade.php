@extends('layouts.admin')

@section('title', 'Laporan Keuangan - Kasir Resto')
@section('page-title', 'Laporan Keuangan')

@section('content')
    {{-- Error alert --}}
    <div id="report-error"
         class="hidden mb-4 p-3 rounded-lg border border-red-300 bg-red-50 text-red-700 text-sm">
        Terjadi kesalahan saat memuat data laporan. Silakan coba lagi.
    </div>

    {{-- TOP: judul + filter periode + switch chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="lg:col-span-2 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Overview Laporan</h2>
                    <p class="text-xs text-gray-500">
                        Ringkasan omzet dan keuntungan berdasarkan periode.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <select id="periodFilter"
                            class="border border-[#57A0D3]/60 text-xs rounded-full px-3 py-1.5
                                   focus:outline-none focus:ring-2 focus:ring-[#57A0D3] focus:border-[#57A0D3]">
                        <option value="daily">Harian</option>
                        <option value="weekly">Mingguan</option>
                        <option value="monthly">Bulanan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
            </div>

            {{-- Switch Line / Bar --}}
            <div class="inline-flex bg-[#E8F2FF] rounded-full p-1 w-max">
                <button id="chartLineBtn"
                        class="chart-type-btn px-4 py-1.5 text-xs font-semibold rounded-full bg-white text-[#57A0D3] shadow">
                    Line Chart
                </button>
                <button id="chartBarBtn"
                        class="chart-type-btn px-4 py-1.5 text-xs font-semibold rounded-full text-[#57A0D3]/70">
                    Bar Chart
                </button>
            </div>
        </div>

        {{-- Kartu kecil total atas --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-xl bg-white shadow-sm border border-[#57A0D3]/20 p-3 flex flex-col">
                <span class="text-[11px] text-gray-500 font-semibold">Total Penjualan</span>
                <span id="summary-total-sales" class="mt-1 text-lg font-extrabold text-[#57A0D3]">
                    Rp 0
                </span>
                <span class="mt-auto text-[10px] text-gray-400">Akumulasi periode tampil</span>
            </div>
            <div class="rounded-xl bg-white shadow-sm border border-emerald-500/20 p-3 flex flex-col">
                <span class="text-[11px] text-gray-500 font-semibold">Total Keuntungan</span>
                <span id="summary-total-profit" class="mt-1 text-lg font-extrabold text-emerald-600">
                    Rp 0
                </span>
                <span class="mt-auto text-[10px] text-gray-400">Setelah dikurangi modal</span>
            </div>
        </div>
    </div>

    {{-- CHART + ringkasan tabel --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        {{-- Chart card --}}
        <div class="xl:col-span-2 rounded-2xl bg-white shadow-md border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Overview</p>
                    <p id="summary-period-label" class="text-[11px] text-gray-400">
                        Harian • 7 hari terakhir
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
                <canvas id="profitChart" class="w-full h-[260px]"></canvas>
            </div>
            <div class="px-5 pb-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead>
                            <tr class="text-[10px] uppercase text-gray-400 border-b border-gray-100">
                                <th class="py-2 text-left">Periode</th>
                                <th class="py-2 text-right">Penjualan</th>
                                <th class="py-2 text-right">Profit</th>
                                <th class="py-2 text-right">Qty</th>
                                <th class="py-2 text-right">Trx</th>
                            </tr>
                        </thead>
                        <tbody id="profit-summary-body">
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-400">
                                    Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kartu kecil mirip template: omzet/profit/qty/trx --}}
        <div class="space-y-3">
            <div class="rounded-2xl p-4 bg-gradient-to-r from-[#57A0D3] to-sky-500 text-white shadow-md">
                <p class="text-[11px] uppercase font-semibold opacity-80">Omzet Bulan Ini</p>
                <p id="card-total-sales" class="mt-1 text-2xl font-extrabold">Rp 0</p>
                <p class="mt-1 text-[11px] opacity-80">Periode aktif di filter</p>
            </div>
            <div class="rounded-2xl p-4 bg-gradient-to-r from-emerald-500 to-lime-500 text-white shadow-md">
                <p class="text-[11px] uppercase font-semibold opacity-80">Keuntungan</p>
                <p id="card-total-profit" class="mt-1 text-2xl font-extrabold">Rp 0</p>
                <p class="mt-1 text-[11px] opacity-80">Setelah modal</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-3">
                    <p class="text-[11px] text-gray-500 font-semibold">Transaksi</p>
                    <p id="card-total-trx" class="mt-1 text-lg font-bold text-gray-800">0</p>
                </div>
                <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-3">
                    <p class="text-[11px] text-gray-500 font-semibold">Produk Terjual</p>
                    <p id="card-total-qty" class="mt-1 text-lg font-bold text-gray-800">0</p>
                </div>
            </div>
        </div>
    </div>

    {{-- TOP PRODUK + KATEGORI --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Top Produk --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-[#57A0D3]/5">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Top Produk</p>
                    <p class="text-[11px] text-gray-500">Berdasarkan keuntungan pada periode aktif.</p>
                </div>
            </div>
            <div class="px-4 py-3">
                <input type="text" id="searchProduct"
                       placeholder="Cari produk..."
                       class="w-full text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#57A0D3]">
            </div>
            <div class="max-h-80 overflow-y-auto">
                <table class="min-w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] uppercase text-gray-400">
                            <th class="px-4 py-2 text-left">Produk</th>
                            <th class="px-4 py-2 text-right">Qty</th>
                            <th class="px-4 py-2 text-right">Omzet</th>
                            <th class="px-4 py-2 text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody id="profit-by-product-body">
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-400">
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Kategori --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-[#57A0D3]/5">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Top Kategori</p>
                    <p class="text-[11px] text-gray-500">Kategori dengan kontribusi profit terbesar.</p>
                </div>
            </div>
            <div class="px-4 py-3">
                <input type="text" id="searchCategory"
                       placeholder="Cari kategori..."
                       class="w-full text-xs border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#57A0D3]">
            </div>
            <div class="max-h-80 overflow-y-auto">
                <table class="min-w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] uppercase text-gray-400">
                            <th class="px-4 py-2 text-left">Kategori</th>
                            <th class="px-4 py-2 text-right">Qty</th>
                            <th class="px-4 py-2 text-right">Omzet</th>
                            <th class="px-4 py-2 text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody id="profit-by-category-body">
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-400">
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- KASBON --}}
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-[#57A0D3]/5 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-800">Daftar Kasbon Berjalan</p>
                <p class="text-[11px] text-gray-500">Data diambil dari transaksi dengan status kasbon.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400">
                        <th class="px-4 py-2 text-left">Pelanggan</th>
                        <th class="px-4 py-2 text-right">Total</th>
                        <th class="px-4 py-2 text-right">Dibayar</th>
                        <th class="px-4 py-2 text-right">Sisa</th>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="kasbon-body">
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-400">
                            Memuat data kasbon...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const primaryBlue = '#57A0D3';
        const profitGreen = '#16A34A';

        let chartType = 'line';
        let profitChart = null;

        const currencyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        });

        function formatCurrency(val) {
            return currencyFormatter.format(Number(val || 0));
        }

        function formatPeriodLabel(period, raw) {
            const d = new Date(raw);
            if (isNaN(d)) return raw;

            if (period === 'daily') {
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
            } else if (period === 'weekly') {
                return 'Minggu ' + d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
            } else if (period === 'monthly') {
                return d.toLocaleDateString('id-ID', { month: 'short', year: '2-digit' });
            } else {
                return d.getFullYear();
            }
        }

        async function fetchJson(url) {
            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return await res.json();
            } catch (e) {
                console.error(e);
                document.getElementById('report-error').classList.remove('hidden');
                throw e;
            }
        }

        function renderProfitChart(type, labels, salesData, profitData) {
            const ctx = document.getElementById('profitChart').getContext('2d');
            if (profitChart) profitChart.destroy();

            if (type === 'bar') {
                profitChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Penjualan',
                                data: salesData,
                                backgroundColor: primaryBlue,
                                borderRadius: 6,
                            },
                            {
                                label: 'Profit',
                                data: profitData,
                                backgroundColor: profitGreen,
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.dataset.label + ': ' + formatCurrency(ctx.parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => formatCurrency(value).replace('Rp', 'Rp ')
                                }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } else {
                profitChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Penjualan',
                                data: salesData,
                                borderColor: primaryBlue,
                                backgroundColor: primaryBlue + '33',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 2
                            },
                            {
                                label: 'Profit',
                                data: profitData,
                                borderColor: profitGreen,
                                backgroundColor: profitGreen + '20',
                                fill: false,
                                tension: 0.4,
                                pointRadius: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ctx.dataset.label + ': ' + formatCurrency(ctx.parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => formatCurrency(value).replace('Rp', 'Rp ')
                                }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
        }

        async function loadProfitSummary() {
            const period = document.getElementById('periodFilter').value;
            const tbody = document.getElementById('profit-summary-body');
            tbody.innerHTML = `<tr><td colspan="5" class="py-4 text-center text-gray-400">Memuat data...</td></tr>`;

            const data = await fetchJson(`{{ route('admin.reports.summary') }}?period=${period}`);

            let html = '';
            let totalSales = 0;
            let totalProfit = 0;
            let totalTrx = 0;
            let totalQty = 0;

            const labels = [];
            const salesData = [];
            const profitData = [];

            if (!data || data.length === 0) {
                html = `<tr><td colspan="5" class="py-4 text-center text-gray-400">Belum ada data.</td></tr>`;
            } else {
                data.forEach(row => {
                    const label = formatPeriodLabel(period, row.period_label);
                    const sales = Number(row.total_sales || 0);
                    const profit = Number(row.total_profit || 0);
                    const qty = Number(row.total_qty || 0);
                    const trx = Number(row.transaksi || 0);

                    labels.push(label);
                    salesData.push(sales);
                    profitData.push(profit);

                    totalSales += sales;
                    totalProfit += profit;
                    totalTrx += trx;
                    totalQty += qty;

                    html += `
                        <tr class="border-b border-gray-50 hover:bg-gray-50">
                            <td class="px-2 py-1.5 text-[11px] text-gray-700">${label}</td>
                            <td class="px-2 py-1.5 text-[11px] text-right text-gray-700">${formatCurrency(sales)}</td>
                            <td class="px-2 py-1.5 text-[11px] text-right text-emerald-600 font-semibold">${formatCurrency(profit)}</td>
                            <td class="px-2 py-1.5 text-[11px] text-right text-gray-600">${qty}</td>
                            <td class="px-2 py-1.5 text-[11px] text-right text-gray-600">${trx}</td>
                        </tr>
                    `;
                });
            }

            tbody.innerHTML = html;

            // Update summary cards
            document.getElementById('summary-total-sales').textContent = formatCurrency(totalSales);
            document.getElementById('summary-total-profit').textContent = formatCurrency(totalProfit);
            document.getElementById('card-total-sales').textContent = formatCurrency(totalSales);
            document.getElementById('card-total-profit').textContent = formatCurrency(totalProfit);
            document.getElementById('card-total-trx').textContent = totalTrx;
            document.getElementById('card-total-qty').textContent = totalQty;

            const labelMap = {
                daily: 'Harian • 7 hari terakhir',
                weekly: 'Mingguan • 12 minggu terakhir',
                monthly: 'Bulanan • 12 bulan terakhir',
                yearly: 'Tahunan • 5 tahun terakhir',
            };
            document.getElementById('summary-period-label').textContent = labelMap[period] || '';

            renderProfitChart(chartType, labels, salesData, profitData);
        }

        async function loadProfitByProduct() {
            const period = document.getElementById('periodFilter').value;
            const tbody = document.getElementById('profit-by-product-body');
            tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Memuat data...</td></tr>`;

            const data = await fetchJson(`{{ route('admin.reports.profitByProduct') }}?period=${period}`);

            window.__rawProducts = data || [];

            renderProductTable();
        }

        function renderProductTable() {
            const tbody = document.getElementById('profit-by-product-body');
            const keyword = (document.getElementById('searchProduct').value || '').toLowerCase();
            const data = (window.__rawProducts || []).filter(row => {
                const name = (row.product_name || '').toString().toLowerCase();
                return !keyword || name.includes(keyword);
            });

            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Belum ada data.</td></tr>`;
                return;
            }

            let html = '';
            data.slice(0, 20).forEach(row => {
                html += `
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-4 py-2 text-[11px] text-gray-800">${row.product_name}</td>
                        <td class="px-4 py-2 text-[11px] text-right text-gray-600">${row.total_qty ?? 0}</td>
                        <td class="px-4 py-2 text-[11px] text-right text-gray-600">${formatCurrency(row.total_sales)}</td>
                        <td class="px-4 py-2 text-[11px] text-right text-emerald-600 font-semibold">${formatCurrency(row.total_profit)}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        async function loadProfitByCategory() {
            const period = document.getElementById('periodFilter').value;
            const tbody = document.getElementById('profit-by-category-body');
            tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Memuat data...</td></tr>`;

            const data = await fetchJson(`{{ route('admin.reports.profitByCategory') }}?period=${period}`);

            window.__rawCategories = data || [];
            renderCategoryTable();
        }

        function renderCategoryTable() {
            const tbody = document.getElementById('profit-by-category-body');
            const keyword = (document.getElementById('searchCategory').value || '').toLowerCase();
            const data = (window.__rawCategories || []).filter(row => {
                const name = (row.category_name || '').toString().toLowerCase();
                return !keyword || name.includes(keyword);
            });

            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Belum ada data.</td></tr>`;
                return;
            }

            let html = '';
            data.forEach(row => {
                html += `
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-4 py-2 text-[11px] text-gray-800">${row.category_name}</td>
                        <td class="px-4 py-2 text-[11px] text-right text-gray-600">${row.total_qty ?? 0}</td>
                        <td class="px-4 py-2 text-[11px] text-right text-gray-600">${formatCurrency(row.total_sales)}</td>
                        <td class="px-4 py-2 text-[11px] text-right text-emerald-600 font-semibold">${formatCurrency(row.total_profit)}</td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        async function loadKasbon() {
            const tbody = document.getElementById('kasbon-body');
            tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-4 text-center text-gray-400">Memuat data kasbon...</td></tr>`;

            const data = await fetchJson(`{{ route('admin.reports.kasbon') }}`);

            if (!data || data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="px-4 py-4 text-center text-gray-400">Tidak ada kasbon berjalan.</td></tr>`;
                return;
            }

            let html = '';
            data.forEach(row => {
                const total = Number(row.total_amount || 0);
                const paid = Number(row.paid_amount || 0);
                const remain = total - paid;
                const createdAt = row.created_at
                    ? new Date(row.created_at).toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                    : '-';

                html += `
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="px-4 py-2 text-[11px] text-gray-800">${row.customer_name ?? '-'}</td>
                        <td class="px-4 py-2 text-[11px] text-right text-gray-700">${formatCurrency(total)}</td>
                        <td class="px-4 py-2 text-[11px] text-right text-gray-700">${formatCurrency(paid)}</td>
                        <td class="px-4 py-2 text-[11px] text-right ${remain > 0 ? 'text-red-600 font-semibold' : 'text-gray-700'}">
                            ${formatCurrency(remain)}
                        </td>
                        <td class="px-4 py-2 text-[11px] text-gray-600">${createdAt}</td>
                        <td class="px-4 py-2 text-[11px] text-center">
                            <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-semibold
                                ${remain > 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'}">
                                ${remain > 0 ? 'Belum Lunas' : 'Lunas'}
                            </span>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
        }

        async function loadAll() {
            document.getElementById('report-error').classList.add('hidden');
            await Promise.all([
                loadProfitSummary(),
                loadProfitByProduct(),
                loadProfitByCategory(),
                loadKasbon(),
            ]);
        }

        document.addEventListener('DOMContentLoaded', () => {
            // period change
            document.getElementById('periodFilter').addEventListener('change', loadAll);

            // chart type switch
            document.getElementById('chartLineBtn').addEventListener('click', () => {
                chartType = 'line';
                document.getElementById('chartLineBtn').classList.add('bg-white', 'shadow');
                document.getElementById('chartLineBtn').classList.remove('text-[#57A0D3]/70');
                document.getElementById('chartBarBtn').classList.remove('bg-white', 'shadow');
                document.getElementById('chartBarBtn').classList.add('text-[#57A0D3]/70');
                loadProfitSummary();
            });

            document.getElementById('chartBarBtn').addEventListener('click', () => {
                chartType = 'bar';
                document.getElementById('chartBarBtn').classList.add('bg-white', 'shadow');
                document.getElementById('chartBarBtn').classList.remove('text-[#57A0D3]/70');
                document.getElementById('chartLineBtn').classList.remove('bg-white', 'shadow');
                document.getElementById('chartLineBtn').classList.add('text-[#57A0D3]/70');
                loadProfitSummary();
            });

            // search filters
            document.getElementById('searchProduct').addEventListener('input', renderProductTable);
            document.getElementById('searchCategory').addEventListener('input', renderCategoryTable);

            loadAll();
        });
    </script>
@endpush
