{{-- resources/views/admin/products/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Stok Produk - Kasir Resto')
@section('page-title', 'Stok Produk')

@section('content')
    @php
        $productsArray = $products->map(function ($p) {
            return [
                'id'            => $p->id,
                'name'          => $p->name,
                'category_id'   => $p->category_id,
                'category_name' => optional($p->category)->name,
                'price'         => (float) $p->price,
                'cost_price'    => (float) $p->cost_price,
                'stock'         => (int) $p->stock,
                'image_url'     => $p->image_url ?? ($p->image_path ? asset('storage/' . $p->image_path) : null),
                'edit_url'      => route('admin.products.edit', $p),
            ];
        });
    @endphp

    {{-- ALERT SUKSES / ERROR --}}
    @if (session('success'))
        <div
            class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 006.293 10.707l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div
            class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 space-y-1">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- TOOLBAR ATAS --}}
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Stok & Produk</p>
            <p class="text-sm text-gray-600">Pantau stok sekaligus kelola data produk dari satu halaman.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center gap-2 rounded-full bg-[#57A0D3] px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-sky-600 transition">
                <i class='bx bx-plus-circle text-base'></i>
                <span>Tambah Produk (CRUD)</span>
            </a>
        </div>
    </div>

    {{-- HEADER + SUMMARY --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <div class="lg:col-span-2 flex flex-col justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Monitoring Stok Produk</h2>
                <p class="text-xs text-gray-500 mt-1">
                    Pantau jumlah stok, harga jual, dan modal produk di toko Anda.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label class="text-[11px] font-semibold text-gray-500">Pencarian</label>
                    <div
                        class="mt-1 flex items-center gap-2 bg-white border border-gray-200 rounded-full px-3 py-1.5 shadow-sm">
                        <i class='bx bx-search text-gray-400 text-base'></i>
                        <input id="searchInput" type="text" placeholder="Cari nama produk / kategori..."
                               class="w-full text-xs outline-none border-0 focus:ring-0">
                    </div>
                </div>
                <div class="sm:w-48">
                    <label class="text-[11px] font-semibold text-gray-500">Kategori</label>
                    <div
                        class="mt-1 bg-white border border-gray-200 rounded-full px-3 py-1.5 shadow-sm flex items-center justify-between">
                        <select id="categoryFilter"
                                class="w-full bg-transparent text-xs outline-none border-0 focus:ring-0">
                            <option value="">Semua kategori</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <i class='bx bx-chevron-down text-gray-400 text-lg ml-1'></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-2 gap-3 lg:col-span-2">
            <div class="rounded-2xl p-4 bg-gradient-to-r from-[#57A0D3] to-sky-500 text-white shadow-md">
                <p class="text-[11px] uppercase tracking-wide font-semibold opacity-90">Total Produk</p>
                <p id="summary-total-products" class="mt-2 text-2xl font-extrabold">{{ $products->count() }}</p>
                <p class="mt-1 text-[11px] opacity-80" id="summary-filter-count">Ditampilkan {{ $products->count() }} produk</p>
            </div>
            <div class="rounded-2xl p-4 bg-white border border-[#57A0D3]/20 shadow-sm flex flex-col">
                <p class="text-[11px] uppercase tracking-wide font-semibold text-gray-500">Total Stok</p>
                <p id="summary-total-stock" class="mt-2 text-2xl font-extrabold text-[#57A0D3]">
                    {{ $products->sum('stock') }}
                </p>
                <p class="mt-1 text-[11px] text-gray-400">Akumulasi semua produk</p>
            </div>
            <div class="rounded-2xl p-4 bg-white border border-amber-300/60 shadow-sm flex flex-col">
                <p class="text-[11px] uppercase tracking-wide font-semibold text-amber-700">Stok Menipis</p>
                @php
                    $lowStockCount = $products->filter(fn ($p) => $p->stock > 0 && $p->stock <= 5)->count();
                @endphp
                <p id="summary-low-stock" class="mt-2 text-2xl font-extrabold text-amber-600">
                    {{ $lowStockCount }}
                </p>
                <p class="mt-1 text-[11px] text-amber-700/80">Stok &le; 5 pcs</p>
            </div>
            <div class="rounded-2xl p-4 bg-white border border-red-300/60 shadow-sm flex flex-col">
                <p class="text-[11px] uppercase tracking-wide font-semibold text-red-700">Habis</p>
                @php
                    $outStockCount = $products->filter(fn ($p) => $p->stock <= 0)->count();
                @endphp
                <p id="summary-out-stock" class="mt-2 text-2xl font-extrabold text-red-600">
                    {{ $outStockCount }}
                </p>
                <p class="mt-1 text-[11px] text-red-700/80">Butuh restock segera</p>
            </div>
        </div>
    </div>

    {{-- GRID KARTU PRODUK (MONITORING) --}}
    <div id="productsEmptyState" class="{{ $products->isEmpty() ? '' : 'hidden' }}">
        <div
            class="w-full flex flex-col items-center justify-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
            <div class="w-14 h-14 rounded-full bg-[#57A0D3]/10 flex items-center justify-center mb-3">
                <i class='bx bx-package text-[#57A0D3] text-2xl'></i>
            </div>
            <p class="text-sm font-semibold text-gray-700">Belum ada produk terdaftar</p>
            <p class="text-xs text-gray-500 mt-1">Tambahkan produk melalui aplikasi kasir atau menu CRUD admin untuk mulai memantau stok.</p>
        </div>
    </div>

    <div id="productsGridWrapper" class="{{ $products->isEmpty() ? 'hidden' : '' }}">
        <div id="productsGrid"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4">
            {{-- JS akan menggambar kartu di sini --}}
        </div>
    </div>

    {{-- SECTION CRUD TABEL (tetap kita biarkan, biar lengkap) --}}
    <div class="mt-10 space-y-3">
        <div class="flex items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Kelola Data Produk</h3>
                <p class="text-xs text-gray-500">Tabel sederhana untuk edit / hapus produk dari admin.</p>
            </div>
            <a href="{{ route('admin.products.create') }}"
               class="inline-flex items-center gap-1 rounded-lg border border-[#57A0D3]/40 bg-white px-3 py-1.5 text-[11px] font-semibold text-[#57A0D3] hover:bg-[#57A0D3]/5">
                <i class='bx bx-plus text-sm'></i>
                <span>Tambah Produk</span>
            </a>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full text-xs">
                <thead class="bg-gray-50">
                    <tr class="text-[11px] uppercase tracking-wide text-gray-500">
                        <th class="px-3 py-2 text-left">Produk</th>
                        <th class="px-3 py-2 text-left">Kategori</th>
                        <th class="px-3 py-2 text-right">Harga Jual</th>
                        <th class="px-3 py-2 text-right">Modal</th>
                        <th class="px-3 py-2 text-center">Stok</th>
                        <th class="px-3 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-t border-gray-100 hover:bg-gray-50/70">
                            <td class="px-3 py-2">
                                <div class="flex items-center gap-2">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}"
                                             alt="{{ $product->name }}"
                                             class="h-8 w-8 rounded-lg object-cover">
                                    @else
                                        <div class="h-8 w-8 rounded-lg bg-[#57A0D3]/10 flex items-center justify-center">
                                            <i class='bx bx-cube text-[#57A0D3] text-lg'></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-xs font-semibold text-gray-800 line-clamp-1">
                                            {{ $product->name }}
                                        </p>
                                        @if($product->description)
                                            <p class="text-[10px] text-gray-500 line-clamp-1">
                                                {{ $product->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600">
                                {{ optional($product->category)->name ?? '-' }}
                            </td>
                            <td class="px-3 py-2 text-xs text-right text-emerald-600 font-semibold">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2 text-xs text-right text-gray-700">
                                Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2 text-xs text-center">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold
                                        @if($product->stock <= 0)
                                            bg-red-100 text-red-700
                                        @elseif($product->stock <= 5)
                                            bg-amber-100 text-amber-700
                                        @else
                                            bg-emerald-100 text-emerald-700
                                        @endif
                                    ">
                                    {{ $product->stock }} pcs
                                </span>
                            </td>
                            <td class="px-3 py-2 text-xs text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="inline-flex items-center rounded-full bg-sky-50 px-2 py-1 text-[10px] font-semibold text-sky-700 hover:bg-sky-100">
                                        <i class='bx bx-edit-alt text-sm mr-0.5'></i> Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin menghapus {{ $product->name }} ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-[10px] font-semibold text-red-600 hover:bg-red-100">
                                            <i class='bx bx-trash text-sm mr-0.5'></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-xs text-gray-500">
                                Belum ada produk terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const PRIMARY_BLUE = '#57A0D3';

        const ALL_PRODUCTS = @json($productsArray);

        const currencyFormatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        });

        function formatCurrency(val) {
            return currencyFormatter.format(Number(val || 0));
        }

        function getStockBadge(stock) {
            if (stock <= 0) {
                return {
                    text: 'Habis',
                    class: 'bg-red-100 text-red-700'
                };
            }
            if (stock <= 5) {
                return {
                    text: 'Menipis',
                    class: 'bg-amber-100 text-amber-700'
                };
            }
            return {
                text: 'Aman',
                class: 'bg-emerald-100 text-emerald-700'
            };
        }

        function renderProducts() {
            const grid = document.getElementById('productsGrid');
            const searchValue = (document.getElementById('searchInput').value || '').toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;

            let filtered = ALL_PRODUCTS.filter(p => {
                const matchSearch = !searchValue
                    || (p.name || '').toLowerCase().includes(searchValue)
                    || (p.category_name || '').toLowerCase().includes(searchValue);

                const matchCategory = !categoryFilter
                    || String(p.category_id) === String(categoryFilter);

                return matchSearch && matchCategory;
            });

            document.getElementById('summary-filter-count').textContent =
                'Ditampilkan ' + filtered.length + ' produk';

            if (!filtered.length) {
                grid.innerHTML = `
                    <div class="col-span-full flex flex-col items-center justify-center py-14 bg-white rounded-2xl border border-dashed border-gray-300">
                        <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                            <i class='bx bx-search text-gray-400 text-xl'></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Produk tidak ditemukan</p>
                        <p class="text-xs text-gray-500 mt-1">Coba ubah kata kunci pencarian atau filter kategori.</p>
                    </div>
                `;
                return;
            }

            filtered = filtered.sort((a, b) => a.name.localeCompare(b.name));

            let html = '';

            filtered.forEach(p => {
                const badge = getStockBadge(p.stock);
                const profitPerUnit = (p.price || 0) - (p.cost_price || 0);

                html += `
                    <div class="group rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-150 flex flex-col cursor-pointer"
                         onclick="window.location.href='${p.edit_url}'">
                        <div class="flex items-start gap-3 px-4 pt-4">
                            <div class="w-12 h-12 rounded-2xl bg-[${PRIMARY_BLUE}]/10 flex items-center justify-center">
                                <i class='bx bx-cube text-[${PRIMARY_BLUE}] text-xl'></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800 line-clamp-2">${p.name}</p>
                                <p class="mt-1 text-[11px] text-gray-500">
                                    ${(p.category_name || 'Tanpa kategori')}
                                </p>
                            </div>
                        </div>

                        <div class="px-4 mt-3 flex items-center justify-between gap-2">
                            <div>
                                <p class="text-[11px] text-gray-500">Harga jual</p>
                                <p class="text-sm font-bold text-emerald-600">${formatCurrency(p.price)}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[11px] text-gray-500">Modal</p>
                                <p class="text-sm font-semibold text-gray-700">${formatCurrency(p.cost_price)}</p>
                            </div>
                        </div>

                        <div class="px-4 mt-2 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold ${badge.class}">
                                    ${badge.text}
                                </span>
                                <span class="text-[11px] text-gray-500">Stok: <span class="font-semibold text-gray-800">${p.stock} pcs</span></span>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400">Profit / pcs</p>
                                <p class="text-[11px] font-semibold ${profitPerUnit >= 0 ? 'text-emerald-600' : 'text-red-600'}">
                                    ${formatCurrency(profitPerUnit)}
                                </p>
                            </div>
                        </div>

                        ${
                            p.image_url
                                ? `
                                  <div class="mt-3 px-4 pb-4">
                                      <div class="overflow-hidden rounded-xl h-28 bg-gray-50">
                                          <img src="${p.image_url}" alt="${p.name}"
                                               class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                      </div>
                                  </div>
                                  `
                                : `<div class="h-3"></div>`
                        }
                    </div>
                `;
            });

            grid.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (!ALL_PRODUCTS.length) return;

            document.getElementById('productsEmptyState')?.classList.add('hidden');
            document.getElementById('productsGridWrapper')?.classList.remove('hidden');

            renderProducts();

            document.getElementById('searchInput').addEventListener('input', () => {
                renderProducts();
            });

            document.getElementById('categoryFilter').addEventListener('change', () => {
                renderProducts();
            });
        });
    </script>
@endpush
