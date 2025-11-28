{{-- resources/views/admin/sales/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Riwayat Transaksi - Kasir Resto')
@section('page-title', 'Riwayat Transaksi')

@section('content')
    @php
        $totalTrans = $sales->count();
        $totalOmzet = $sales->sum('total_amount');
        $totalKasbon = $sales->where('status', 'kasbon')->sum('total_amount');
        $totalPiutang = $sales->where('status', 'kasbon')->sum(function ($s) {
            $remain = $s->total_amount - $s->paid_amount;
            return $remain > 0 ? $remain : 0;
        });

        $salesArray = $sales->map(function ($s) {
            return [
                'id' => $s->id,
                'total_amount' => (float) $s->total_amount,
                'paid_amount' => (float) $s->paid_amount,
                'change_amount' => (float) $s->change_amount,
                'status' => $s->status,
                'customer_name' => $s->customer_name_snapshot ?? optional($s->customer)->name ?? 'Pelanggan umum',
                'created_at_label' => $s->created_at->timezone(config('app.timezone'))->translatedFormat('d M Y • H:i'),
                'total_qty' => (int) $s->items->sum('qty'),
                'remaining' => max(0, $s->total_amount - $s->paid_amount),
                'items' => $s->items->map(function ($i) {
                    return [
                        'product_name' => optional($i->product)->name ?? '',
                        'qty' => (int) $i->qty,
                        'price' => (float) $i->price,
                        'subtotal' => (float) $i->subtotal,
                    ];
                })->values()->all(),
            ];
        });
    @endphp

    {{-- HEADER JUDUL --}}
    <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-800">Ringkasan Penjualan</h2>
        <p class="text-xs text-gray-500 mt-1">
            Lihat riwayat transaksi kasir: lunas maupun kasbon.
        </p>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
            {{-- Total transaksi --}}
            <div class="rounded-2xl p-4 bg-gradient-to-r from-[#57A0D3] to-sky-500 text-white shadow-md">
                <p class="text-[11px] uppercase tracking-wide font-semibold opacity-90">Total Transaksi</p>
                <p id="summary-total-trans" class="mt-2 text-2xl font-extrabold">{{ $totalTrans }}</p>
                <p id="summary-filter-count" class="mt-1 text-[11px] opacity-80">
                    Ditampilkan {{ $totalTrans }} transaksi
                </p>
            </div>

            {{-- Omzet --}}
            <div class="rounded-2xl p-4 bg-white border border-[#57A0D3]/20 shadow-sm">
                <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500">Total Omzet</p>
                <p id="summary-total-omzet"
                   class="mt-2 text-xl font-extrabold text-[#57A0D3]">
                    Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-gray-400">Akumulasi semua transaksi</p>
            </div>

            {{-- Nominal kasbon --}}
            <div class="rounded-2xl p-4 bg-white border border-amber-300/60 shadow-sm">
                <p class="text-[11px] uppercase tracking-wide font-semibold text-amber-700">Total Kasbon</p>
                <p class="mt-2 text-xl font-extrabold text-amber-600">
                    Rp {{ number_format($totalKasbon, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-amber-700/80">Nominal transaksi berstatus utang</p>
            </div>

            {{-- Piutang berjalan --}}
            <div class="rounded-2xl p-4 bg-white border border-red-300/60 shadow-sm">
                <p class="text-[11px] uppercase tracking-wide font-semibold text-red-700">Piutang Berjalan</p>
                <p id="summary-total-piutang"
                   class="mt-2 text-xl font-extrabold text-red-600">
                    Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-[11px] text-red-700/80">Sisa utang yang belum dibayar</p>
            </div>
        </div>
    </div>

    {{-- SECTION LIST + SEARCH + FILTER --}}
    <div class="mb-5 space-y-3">
        <div>
            <h3 class="text-sm sm:text-base font-semibold text-gray-800">Daftar Transaksi</h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Kelola riwayat transaksi, status pembayaran, dan kasbon pelanggan.
            </p>
        </div>

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            {{-- Search di bawah card, full width di kiri --}}
            <div class="w-full md:max-w-md">
                <div
                    class="flex items-center gap-2 px-3 py-2 rounded-full bg-white border border-gray-200 shadow-sm
                           focus-within:ring-1 focus-within:ring-[#57A0D3] focus-within:border-[#57A0D3] transition">
                    <i class='bx bx-search text-gray-400 text-lg'></i>
                    <input
                        id="salesSearchInput"
                        type="text"
                        class="w-full bg-transparent border-none text-xs sm:text-sm focus:outline-none focus:ring-0"
                        placeholder="Cari transaksi (pelanggan, ID, status, nominal)...">
                    <button
                        id="salesSearchClearBtn"
                        type="button"
                        class="hidden text-[11px] text-gray-500 hover:text-gray-700">
                        Reset
                    </button>
                </div>
                <p id="salesSearchHint" class="mt-1 text-[10px] text-gray-400">
                    Bisa cari berdasarkan nama pelanggan, ID transaksi, status, atau jumlah.
                </p>
            </div>

            {{-- Tab filter di kanan --}}
            <div class="w-full md:w-auto">
                <div
                    id="filterTabsWrapper"
                    class="flex items-center bg-[#E8F2FF] rounded-full p-1 shadow-sm text-[12px] md:ml-auto w-full md:w-auto">
                    <button type="button"
                            data-filter="all"
                            class="flex-1 md:flex-none px-3 py-1.5 rounded-full text-center font-semibold bg-white text-[#57A0D3] shadow">
                        Semua
                    </button>
                    <button type="button"
                            data-filter="paid"
                            class="flex-1 md:flex-none px-3 py-1.5 rounded-full text-center text-gray-600 font-medium">
                        Lunas
                    </button>
                    <button type="button"
                            data-filter="kasbon"
                            class="flex-1 md:flex-none px-3 py-1.5 rounded-full text-center text-gray-600 font-medium">
                        Utang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- LIST / EMPTY STATE --}}
    <div id="salesEmptyState" class="{{ $sales->isEmpty() ? '' : 'hidden' }}">
        <div
            class="w-full flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
            <div class="w-14 h-14 rounded-full bg-[#57A0D3]/10 flex items-center justify-center mb-3">
                <i class='bx bx-receipt text-[#57A0D3] text-2xl'></i>
            </div>
            <p class="text-sm font-semibold text-gray-700">Belum ada riwayat transaksi</p>
            <p class="text-xs text-gray-500 mt-1">
                Transaksi yang dibuat kasir akan muncul di sini secara otomatis.
            </p>
        </div>
    </div>

    {{-- EMPTY STATE KHUSUS FILTER + SEARCH --}}
    <div id="salesFilteredEmptyState" class="hidden">
        <div
            class="w-full flex flex-col items-center justify-center py-10 bg-white rounded-2xl border border-dashed border-gray-300">
            <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center mb-3">
                <i class='bx bx-search-alt text-amber-500 text-2xl'></i>
            </div>
            <p class="text-sm font-semibold text-gray-700">Tidak ada transaksi yang cocok</p>
            <p class="text-xs text-gray-500 mt-1 text-center max-w-sm">
                Coba ubah kata kunci pencarian atau filter status untuk melihat transaksi lainnya.
            </p>
        </div>
    </div>

    <div id="salesListWrapper" class="{{ $sales->isEmpty() ? 'hidden' : '' }}">
        <div id="salesList" class="space-y-3">
            {{-- akan di-render via JS --}}
        </div>
    </div>

    {{-- MODAL DETAIL TRANSAKSI --}}
    <div id="saleDetailModal"
         class="fixed inset-0 z-40 hidden">
        <div class="absolute inset-0 bg-black/30" onclick="closeSaleDetail()"></div>

        <div
            class="relative z-10 max-w-xl w-full mx-auto my-10 bg-white rounded-2xl shadow-xl p-5 max-h-[80vh] flex flex-col">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 id="detail-title" class="text-base font-bold text-gray-800">
                        Detail Transaksi
                    </h3>
                    <p id="detail-date" class="text-xs text-gray-500 mt-1"></p>
                    <p id="detail-customer" class="text-xs text-gray-600 mt-1"></p>
                </div>
                <button type="button" onclick="closeSaleDetail()"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200">
                    <i class='bx bx-x text-lg'></i>
                </button>
            </div>

            <div class="flex items-center justify-between mb-3">
                <div class="text-sm">
                    <p class="text-[11px] text-gray-500">Total Belanja</p>
                    <p id="detail-total" class="text-lg font-extrabold text-gray-800"></p>
                </div>
                <div class="text-right text-xs">
                    <div id="detail-status-chip"
                         class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold">
                        {{-- status --}}
                    </div>
                </div>
            </div>

            <div class="mb-3 w-full px-3 py-2 rounded-xl bg-[#F3F6FF] flex items-center justify-between text-xs">
                <span>Total barang dibeli</span>
                <span id="detail-total-qty" class="font-semibold"></span>
            </div>

            <div class="flex-1 overflow-y-auto border-t border-gray-100 pt-2 mt-1">
                <ul id="detail-items" class="divide-y divide-gray-100 text-xs">
                    {{-- items --}}
                </ul>
            </div>

            <div class="border-t border-gray-100 mt-3 pt-3 text-xs space-y-1">
                <div class="flex justify-between">
                    <span>Dibayar</span>
                    <span id="detail-paid" class="font-medium"></span>
                </div>
                <div class="flex justify-between">
                    <span>Kembalian</span>
                    <span id="detail-change" class="font-medium"></span>
                </div>
                <div id="detail-remaining-wrapper" class="flex justify-between hidden">
                    <span class="text-red-700">Sisa utang</span>
                    <span id="detail-remaining" class="font-semibold text-red-700"></span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const PRIMARY_BLUE = '#57A0D3';
        const ALL_SALES = @json($salesArray);

        let currentFilter = 'all';
        let currentSearch = '';

        const currencyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        });

        function formatCurrency(value) {
            return currencyFormatter.format(Number(value || 0));
        }

        function getStatusMeta(status) {
            if (status === 'kasbon') {
                return {
                    label: 'Utang',
                    bg: 'bg-amber-100',
                    text: 'text-amber-700',
                    strip: '#FFA726',
                };
            }
            return {
                label: 'Lunas',
                bg: 'bg-emerald-100',
                text: 'text-emerald-700',
                strip: PRIMARY_BLUE,
            };
        }

        function normalize(str) {
            return (str || '').toString().toLowerCase();
        }

        function matchesSearch(sale, keyword) {
            if (!keyword) return true;
            const q = normalize(keyword);

            const fields = [
                sale.id?.toString(),
                sale.customer_name,
                sale.status,
                sale.created_at_label,
                sale.total_amount?.toString(),
                sale.paid_amount?.toString(),
                sale.remaining?.toString(),
            ];

            return fields.some(f => normalize(f).includes(q));
        }

        function renderSales() {
            const listEl = document.getElementById('salesList');
            const emptyEl = document.getElementById('salesEmptyState');
            const filteredEmptyEl = document.getElementById('salesFilteredEmptyState');
            const wrapperEl = document.getElementById('salesListWrapper');

            let filtered = ALL_SALES.filter(s => {
                if (currentFilter === 'paid' && s.status !== 'paid') return false;
                if (currentFilter === 'kasbon' && s.status !== 'kasbon') return false;
                if (!matchesSearch(s, currentSearch)) return false;
                return true;
            });

            document.getElementById('summary-filter-count').textContent =
                'Ditampilkan ' + filtered.length + ' transaksi';

            const hasOriginalData = ALL_SALES.length > 0;
            const hasSearchOrFilter = currentFilter !== 'all' || (currentSearch && currentSearch.trim() !== '');

            if (!hasOriginalData) {
                emptyEl.classList.remove('hidden');
                filteredEmptyEl.classList.add('hidden');
                wrapperEl.classList.add('hidden');
                return;
            }

            if (!filtered.length && hasSearchOrFilter) {
                emptyEl.classList.add('hidden');
                filteredEmptyEl.classList.remove('hidden');
                wrapperEl.classList.add('hidden');
                return;
            }

            emptyEl.classList.add('hidden');
            filteredEmptyEl.classList.add('hidden');
            wrapperEl.classList.remove('hidden');

            let html = '';

            filtered.forEach(s => {
                const meta = getStatusMeta(s.status);
                const remaining = Number(s.remaining || 0);

                html += `
                    <div class="bg-white rounded-2xl shadow-sm flex overflow-hidden hover:shadow-md transition-shadow duration-150">
                        <div style="background:${meta.strip}" class="w-1.5 sm:w-2"></div>
                        <button type="button"
                            onclick="openSaleDetail(${s.id})"
                            class="flex-1 text-left px-3 sm:px-4 py-3 sm:py-4">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm sm:text-base font-bold text-gray-800">
                                    ${formatCurrency(s.total_amount)}
                                </p>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold ${meta.bg} ${meta.text}">
                                    ${meta.label}
                                </span>
                            </div>
                            <p class="mt-1 text-[11px] text-gray-500">${s.created_at_label}</p>
                            <p class="mt-1 text-[12px] font-medium text-gray-800">${s.customer_name || 'Pelanggan umum'}</p>
                            <div class="mt-1 flex items-center justify-between text-[11px] text-gray-500">
                                <span>${s.total_qty} barang</span>
                                ${
                                    s.status === 'kasbon' && remaining > 0
                                        ? `<span class="text-red-600 font-semibold">Sisa utang: ${formatCurrency(remaining)}</span>`
                                        : ''
                                }
                            </div>
                        </button>
                    </div>
                `;
            });

            listEl.innerHTML = html;
        }

        function setActiveFilterTab() {
            const wrapper = document.getElementById('filterTabsWrapper');
            if (!wrapper) return;

            const buttons = wrapper.querySelectorAll('button[data-filter]');
            buttons.forEach(btn => {
                const filter = btn.getAttribute('data-filter');
                if (filter === currentFilter) {
                    btn.classList.add('bg-white', 'text-[#57A0D3]', 'shadow');
                    btn.classList.remove('text-gray-600', 'font-medium');
                    btn.classList.add('font-semibold');
                } else {
                    btn.classList.remove('bg-white', 'text-[#57A0D3]', 'shadow', 'font-semibold');
                    btn.classList.add('text-gray-600', 'font-medium');
                }
            });
        }

        function openSaleDetail(id) {
            const sale = ALL_SALES.find(s => s.id === id);
            if (!sale) return;

            const meta = getStatusMeta(sale.status);

            document.getElementById('detail-title').textContent = `Detail Transaksi #${sale.id}`;
            document.getElementById('detail-date').textContent = sale.created_at_label;
            document.getElementById('detail-customer').textContent = `Pelanggan: ${sale.customer_name || 'Pelanggan umum'}`;
            document.getElementById('detail-total').textContent = formatCurrency(sale.total_amount);
            document.getElementById('detail-paid').textContent = formatCurrency(sale.paid_amount);
            document.getElementById('detail-change').textContent = formatCurrency(sale.change_amount);
            document.getElementById('detail-total-qty').textContent = `${sale.total_qty} pcs`;

            const statusChip = document.getElementById('detail-status-chip');
            statusChip.className =
                'inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold ' +
                meta.bg + ' ' + meta.text;
            statusChip.textContent = meta.label;

            const remaining = Number(sale.remaining || 0);
            const remainingWrapper = document.getElementById('detail-remaining-wrapper');
            if (sale.status === 'kasbon' && remaining > 0) {
                remainingWrapper.classList.remove('hidden');
                document.getElementById('detail-remaining').textContent = formatCurrency(remaining);
            } else {
                remainingWrapper.classList.add('hidden');
            }

            const itemsUl = document.getElementById('detail-items');
            let itemsHtml = '';
            (sale.items || []).forEach(item => {
                itemsHtml += `
                    <li class="py-2 flex">
                        <div class="w-1.5 mt-1.5 mr-3 rounded-full bg-[${PRIMARY_BLUE}]"></div>
                        <div class="flex-1">
                            <p class="text-[13px] font-semibold text-gray-800">${item.product_name}</p>
                            <p class="text-[11px] text-gray-500">${item.qty}x ${formatCurrency(item.price)}</p>
                            <p class="text-[11px] font-semibold mt-0.5">Total: ${formatCurrency(item.subtotal)}</p>
                        </div>
                    </li>
                `;
            });
            itemsUl.innerHTML = itemsHtml || `<li class="py-3 text-center text-gray-400 text-xs">Tidak ada item.</li>`;

            document.getElementById('saleDetailModal').classList.remove('hidden');
        }

        function closeSaleDetail() {
            document.getElementById('saleDetailModal').classList.add('hidden');
        }

        function handleSearchInput() {
            const input = document.getElementById('salesSearchInput');
            const clearBtn = document.getElementById('salesSearchClearBtn');
            currentSearch = input.value || '';

            if (currentSearch.trim() !== '') {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }

            renderSales();
        }

        function clearSearch() {
            const input = document.getElementById('salesSearchInput');
            input.value = '';
            currentSearch = '';
            document.getElementById('salesSearchClearBtn').classList.add('hidden');
            renderSales();
        }

        document.addEventListener('DOMContentLoaded', () => {
            setActiveFilterTab();
            renderSales();

            const wrapper = document.getElementById('filterTabsWrapper');
            if (wrapper) {
                wrapper.querySelectorAll('button[data-filter]').forEach(btn => {
                    btn.addEventListener('click', () => {
                        currentFilter = btn.getAttribute('data-filter');
                        setActiveFilterTab();
                        renderSales();
                    });
                });
            }

            const searchInput = document.getElementById('salesSearchInput');
            const clearBtn = document.getElementById('salesSearchClearBtn');
            if (searchInput) {
                searchInput.addEventListener('input', handleSearchInput);
                searchInput.addEventListener('keyup', (e) => {
                    if (e.key === 'Escape') {
                        clearSearch();
                        searchInput.blur();
                    }
                });
            }
            if (clearBtn) {
                clearBtn.addEventListener('click', clearSearch);
            }
        });
    </script>
@endpush
