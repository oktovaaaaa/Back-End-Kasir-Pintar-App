{{-- resources/views/admin/customers/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kelola Pelanggan - Kasir Resto')
@section('page-title', 'Kelola Pelanggan')

@section('content')
    @php
        $primaryBlue = '#57A0D3';
    @endphp

    {{-- Alert sukses --}}
    @if (session('success'))
        <div class="mb-4 p-3 rounded-lg border border-green-200 bg-green-50 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- WIDGET RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <div class="rounded-2xl border border-slate-100 bg-white px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                 style="background-color: rgba(87,160,211,0.12); color: {{ $primaryBlue }};">
                <i class="bx bx-user text-xl"></i>
            </div>
            <div>
                <p class="text-[11px] text-slate-500">Total pelanggan</p>
                <p class="text-base font-semibold text-slate-800">{{ $totalCustomers ?? 0 }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                 style="background-color: rgba(216, 180, 254, 0.25); color:#6D28D9;">
                <i class="bx bx-user-check text-xl"></i>
            </div>
            <div>
                <p class="text-[11px] text-slate-500">Tanpa utang</p>
                <p class="text-base font-semibold text-slate-800">{{ $noDebtCustomers ?? 0 }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center"
                 style="background-color: rgba(249,115,22,0.15); color:#EA580C;">
                <i class="bx bx-user-voice text-xl"></i>
            </div>
            <div>
                <p class="text-[11px] text-slate-500">Pelanggan berutang</p>
                <p class="text-base font-semibold text-slate-800">{{ $debtCustomers ?? 0 }}</p>
            </div>
        </div>

        <div class="rounded-2xl px-4 py-3 shadow-sm"
             style="background: linear-gradient(135deg, {{ $primaryBlue }}, #1D4ED8); color:white;">
            <p class="text-[11px] text-white/80">Total utang aktif</p>
            <p class="text-lg font-semibold mt-1">
                Rp {{ number_format($totalDebt ?? 0, 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-white/80 mt-1">
                Jumlah akumulasi semua kasbon pelanggan yang belum lunas.
            </p>
        </div>
    </div>

    {{-- Header + tombol tambah --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Pelanggan & Kasbon</h2>
            <p class="text-xs text-slate-500">
                Kelola data pelanggan, status kasbon, dan pembayaran utang.
            </p>
        </div>

        <button id="btnOpenCreate"
                class="inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-semibold text-white shadow-sm"
                style="background-color: {{ $primaryBlue }};">
            <i class="bx bx-user-plus mr-1 text-base"></i>
            Tambah Pelanggan
        </button>
    </div>

    {{-- Search + FILTER STATUS --}}
    <div class="mb-4 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
        <div class="flex-1">
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <i class="bx bx-search text-xl"></i>
                </span>
                <input id="customerSearch"
                       type="text"
                       placeholder="Cari nama pelanggan atau kontak..."
                       class="w-full rounded-full border border-slate-200 pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400 bg-white" />
            </div>
        </div>

        <div class="inline-flex bg-slate-100 rounded-full p-1 text-[11px] md:text-xs">
            <button type="button" data-filter="all"
                    class="filter-chip px-3 py-1 rounded-full font-medium text-slate-600 bg-white shadow-sm">
                Semua
            </button>
            <button type="button" data-filter="debt"
                    class="filter-chip px-3 py-1 rounded-full font-medium text-slate-500">
                Berutang
            </button>
            <button type="button" data-filter="clear"
                    class="filter-chip px-3 py-1 rounded-full font-medium text-slate-500">
                Lunas
            </button>
        </div>
    </div>

    {{-- Tabel pelanggan --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-slate-50 text-[11px] md:text-xs font-semibold text-slate-500 uppercase tracking-wide">
            <div class="grid grid-cols-12 gap-2 px-4 py-3">
                <div class="col-span-5 md:col-span-4">Pelanggan</div>
                <div class="col-span-4 md:col-span-4">Kontak & Instansi</div>
                <div class="col-span-3 md:col-span-2 text-center">Total Utang</div>
                <div class="hidden md:block md:col-span-2 text-center">Aksi</div>
            </div>
        </div>

        <div id="customerTableBody" class="divide-y divide-slate-100 text-sm">
            @forelse($customers as $customer)
                @php
                    $debt = $customer->total_debt ?? 0;
                    $hasDebt = $debt > 0.0001;
                @endphp
                <div class="grid grid-cols-12 gap-2 px-4 py-3 items-center customer-row cursor-pointer hover:bg-slate-50/70 transition"
                     data-name="{{ strtolower($customer->name) }}"
                     data-phone="{{ strtolower($customer->phone ?? '') }}"
                     data-email="{{ strtolower($customer->email ?? '') }}"
                     data-address="{{ strtolower($customer->address ?? '') }}"
                     data-id="{{ $customer->id }}"
                     data-has-debt="{{ $hasDebt ? 1 : 0 }}"
                     data-customer-id="{{ $customer->id }}"
                     data-customer-name="{{ $customer->name }}"
                     data-customer-phone="{{ $customer->phone }}"
                     data-customer-email="{{ $customer->email }}"
                     data-customer-address="{{ $customer->address }}"
                     data-customer-company="{{ $customer->company }}"
                     data-customer-note="{{ $customer->note }}">

                    {{-- Avatar + nama --}}
                    <div class="col-span-5 md:col-span-4 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold"
                             style="background-color: rgba(87,160,211,0.12); color:#1f2933;">
                            {{ strtoupper(mb_substr($customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-slate-800">{{ $customer->name }}</div>
                            @if($customer->company)
                                <div class="text-[11px] text-slate-500">
                                    {{ $customer->company }}
                                </div>
                            @elseif($customer->note)
                                <div class="text-xs text-slate-500 line-clamp-1">{{ $customer->note }}</div>
                            @endif
                        </div>
                    </div>

                    {{-- Kontak + Instansi --}}
                    <div class="col-span-4 md:col-span-4 text-xs md:text-sm">
                        @if($customer->phone)
                            <div class="text-slate-800">{{ $customer->phone }}</div>
                        @endif
                        @if($customer->email)
                            <div class="text-slate-500">{{ $customer->email }}</div>
                        @endif
                        @if($customer->address)
                            <div class="text-slate-500 text-[11px] mt-0.5">{{ $customer->address }}</div>
                        @endif
                    </div>

                    {{-- Total utang --}}
                    <div class="col-span-3 md:col-span-2 flex flex-col items-center justify-center text-xs md:text-sm">
                        @if($hasDebt)
                            <div class="font-semibold text-red-600">
                                Rp {{ number_format($debt, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-red-500">Kasbon aktif</div>
                        @else
                            <div class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Lunas
                            </div>
                        @endif
                    </div>

                    {{-- Aksi --}}
                    <div class="col-span-12 md:col-span-2 mt-2 md:mt-0 flex justify-start md:justify-center gap-2 text-xs">
                        <button type="button"
                                class="btn-edit inline-flex items-center justify-center px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-50">
                            <i class="bx bx-pencil mr-1 text-sm"></i> Edit
                        </button>

                        <button type="button"
                                class="btn-kasbon inline-flex items-center justify-center px-3 py-1.5 rounded-full border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100"
                                @if(!$hasDebt) disabled @endif>
                            <i class="bx bx-receipt mr-1 text-sm"></i> Kasbon
                        </button>

                        <form method="POST"
                              action="{{ route('admin.customers.destroy', $customer) }}"
                              onsubmit="return confirm('Yakin menghapus {{ $customer->name }} ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-100">
                                <i class="bx bx-trash text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-sm text-center text-slate-500">
                    Belum ada pelanggan.
                </div>
            @endforelse
        </div>
    </div>

    {{-- ========= MODAL FORM PELANGGAN (TAMBAH / EDIT) ========= --}}
    <div id="customerModal"
         class="fixed inset-0 z-40 hidden items-center justify-center bg-black/40">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl mx-4 md:mx-0 max-h-[90vh] overflow-y-auto p-6 md:p-8">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 id="customerModalTitle" class="text-lg md:text-xl font-semibold text-slate-800">
                        Tambah Pelanggan
                    </h3>
                    <p class="text-xs md:text-sm text-slate-500">
                        Lengkapi data pelanggan untuk dicatat dalam sistem.
                    </p>
                </div>
                <button type="button" id="btnCloseCustomerModal"
                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200">
                    <i class="bx bx-x text-xl"></i>
                </button>
            </div>

            <form id="customerForm" method="POST" action="{{ route('admin.customers.store') }}">
                @csrf
                <input type="hidden" name="_method" id="customerFormMethod" value="POST">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 text-sm">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama lengkap</label>
                        <input type="text" name="name" id="fieldName"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400"
                               placeholder="Nama pelanggan" required>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">No. Telepon</label>
                        <input type="text" name="phone" id="fieldPhone"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400"
                               placeholder="Contoh: 081234567890">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Email</label>
                        <input type="email" name="email" id="fieldEmail"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400"
                               placeholder="contoh@email.com">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Alamat</label>
                        <input type="text" name="address" id="fieldAddress"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400"
                               placeholder="Alamat lengkap (opsional)">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Instansi / Perusahaan</label>
                        <input type="text" name="company" id="fieldCompany"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400"
                               placeholder="Nama perusahaan (opsional)">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan</label>
                        <textarea name="note" id="fieldNote" rows="3"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400"
                                  placeholder="Catatan khusus pelanggan (opsional)"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3 text-sm">
                    <button type="button" id="btnCancelCustomer"
                            class="px-4 py-2 rounded-full text-slate-500 hover:bg-slate-100">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-full font-semibold text-white shadow-sm"
                            style="background-color: {{ $primaryBlue }};">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========= MODAL KASBON PELANGGAN ========= --}}
    <div id="kasbonModal"
         class="fixed inset-0 z-40 hidden items-center justify-center bg-black/40">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-2xl mx-4 md:mx-0 max-h-[90vh] overflow-y-auto p-6 md:p-8">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg md:text-xl font-semibold text-slate-800">
                        Kasbon Pelanggan
                    </h3>
                    <p id="kasbonCustomerName" class="text-xs md:text-sm text-slate-500">
                        <!-- filled by JS -->
                    </p>
                </div>
                <button type="button" id="btnCloseKasbonModal"
                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200">
                    <i class="bx bx-x text-xl"></i>
                </button>
            </div>

            <div id="kasbonList" class="space-y-3 text-sm">
                <div id="kasbonLoading" class="text-center text-slate-500 text-sm py-6 hidden">
                    Memuat data kasbon...
                </div>
                <div id="kasbonEmpty" class="text-center text-slate-500 text-sm py-6 hidden">
                    Tidak ada kasbon aktif untuk pelanggan ini.
                </div>
                <div id="kasbonItems"></div>
            </div>
        </div>
    </div>

    {{-- ========= MODAL BAYAR KASBON ========= --}}
    <div id="payKasbonModal"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
        <div class="bg-white rounded-3xl shadow-xl w-full max-w-md mx-4 md:mx-0 p-6 md:p-7">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">
                        Bayar Kasbon
                    </h3>
                    <p id="payKasbonInfo" class="text-xs text-slate-500">
                        <!-- filled by JS -->
                    </p>
                </div>
                <button type="button" id="btnClosePayKasbon"
                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200">
                    <i class="bx bx-x text-xl"></i>
                </button>
            </div>

            <form id="payKasbonForm" class="text-sm">
                @csrf
                <input type="hidden" id="paySaleId" value="">
                <input type="hidden" id="payRemainingRaw" value="0">

                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1 text-slate-600">
                        <span>Sisa kasbon</span>
                        <span id="payRemainingLabel" class="font-semibold text-red-600"></span>
                    </div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Nominal bayar
                    </label>
                    <input type="number" id="payAmount" name="amount" min="1"
                           class="w-full rounded-xl border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:border-sky-400"
                           placeholder="Masukkan nominal bayar">
                    <p class="mt-1 text-[11px] text-slate-500">
                        Nilai awal otomatis sesuai sisa kasbon, tapi bisa dikurangi
                        (misalnya pelanggan hanya bayar sebagian).
                    </p>
                    <p id="payKasbonError" class="mt-1 text-[11px] text-red-500 hidden"></p>
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" id="btnCancelPayKasbon"
                            class="px-4 py-2 rounded-full text-slate-500 hover:bg-slate-100 text-sm">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-full font-semibold text-white shadow-sm text-sm"
                            style="background-color: {{ $primaryBlue }};">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ==== SEARCH & FILTER STATUS ====
    const searchInput = document.getElementById('customerSearch');
    const rows = Array.from(document.querySelectorAll('.customer-row'));
    const filterChips = document.querySelectorAll('.filter-chip');
    let currentFilter = 'all';

    function applyFilter() {
        const q = (searchInput?.value || '').toLowerCase();

        rows.forEach(row => {
            const text = (row.dataset.name + ' ' +
                          row.dataset.phone + ' ' +
                          row.dataset.email + ' ' +
                          row.dataset.address).toLowerCase();

            const hasDebt = row.dataset.hasDebt === '1';

            let statusMatch = true;
            if (currentFilter === 'debt') {
                statusMatch = hasDebt;
            } else if (currentFilter === 'clear') {
                statusMatch = !hasDebt;
            }

            const match = text.includes(q) && statusMatch;
            row.style.display = match ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilter);
    }

    filterChips.forEach(chip => {
        chip.addEventListener('click', function () {
            filterChips.forEach(c => c.classList.remove('bg-white', 'shadow-sm', 'text-slate-600'));
            filterChips.forEach(c => c.classList.add('text-slate-500'));

            this.classList.add('bg-white', 'shadow-sm', 'text-slate-600');
            currentFilter = this.dataset.filter;
            applyFilter();
        });
    });

    // ==== ROW CLICK => DETAIL PELANGGAN ====
    rows.forEach(row => {
        row.addEventListener('click', function (e) {
            if (e.target.closest('button, form, input, textarea, i')) {
                return;
            }
            const id = this.dataset.id;
            if (id) {
                window.location.href = "{{ url('admin/customers') }}/" + id;
            }
        });
    });

    // ==== MODAL PELANGGAN (TAMBAH / EDIT) ====
    const customerModal = document.getElementById('customerModal');
    const btnOpenCreate = document.getElementById('btnOpenCreate');
    const btnCloseCustomerModal = document.getElementById('btnCloseCustomerModal');
    const btnCancelCustomer = document.getElementById('btnCancelCustomer');
    const customerModalTitle = document.getElementById('customerModalTitle');
    const customerForm = document.getElementById('customerForm');
    const customerFormMethod = document.getElementById('customerFormMethod');

    const fieldName = document.getElementById('fieldName');
    const fieldPhone = document.getElementById('fieldPhone');
    const fieldEmail = document.getElementById('fieldEmail');
    const fieldAddress = document.getElementById('fieldAddress');
    const fieldCompany = document.getElementById('fieldCompany');
    const fieldNote = document.getElementById('fieldNote');

    function openCustomerModalCreate() {
        customerModalTitle.textContent = 'Tambah Pelanggan';
        customerForm.action = "{{ route('admin.customers.store') }}";
        customerFormMethod.value = 'POST';

        fieldName.value = '';
        fieldPhone.value = '';
        fieldEmail.value = '';
        fieldAddress.value = '';
        fieldCompany.value = '';
        fieldNote.value = '';

        customerModal.classList.remove('hidden');
        customerModal.classList.add('flex');
        fieldName.focus();
    }

    function openCustomerModalEdit(customer) {
        customerModalTitle.textContent = 'Edit Pelanggan';
        customerForm.action = "{{ url('admin/customers') }}/" + customer.id;
        customerFormMethod.value = 'PUT';

        fieldName.value = customer.name || '';
        fieldPhone.value = customer.phone || '';
        fieldEmail.value = customer.email || '';
        fieldAddress.value = customer.address || '';
        fieldCompany.value = customer.company || '';
        fieldNote.value = customer.note || '';

        customerModal.classList.remove('hidden');
        customerModal.classList.add('flex');
        fieldName.focus();
    }

    function closeCustomerModal() {
        customerModal.classList.add('hidden');
        customerModal.classList.remove('flex');
    }

    if (btnOpenCreate) btnOpenCreate.addEventListener('click', openCustomerModalCreate);
    if (btnCloseCustomerModal) btnCloseCustomerModal.addEventListener('click', closeCustomerModal);
    if (btnCancelCustomer) btnCancelCustomer.addEventListener('click', closeCustomerModal);

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const row = this.closest('.customer-row');
            const customerData = {
                id: row.dataset.customerId,
                name: row.dataset.customerName,
                phone: row.dataset.customerPhone,
                email: row.dataset.customerEmail,
                address: row.dataset.customerAddress,
                company: row.dataset.customerCompany,
                note: row.dataset.customerNote,
            };
            openCustomerModalEdit(customerData);
        });
    });

    // ==== MODAL KASBON PELANGGAN ====
    const kasbonModal = document.getElementById('kasbonModal');
    const btnCloseKasbonModal = document.getElementById('btnCloseKasbonModal');
    const kasbonCustomerName = document.getElementById('kasbonCustomerName');
    const kasbonLoading = document.getElementById('kasbonLoading');
    const kasbonEmpty = document.getElementById('kasbonEmpty');
    const kasbonItems = document.getElementById('kasbonItems');

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function openKasbonModal(row) {
        const customer = {
            id: row.dataset.customerId,
            name: row.dataset.customerName,
        };

        kasbonCustomerName.textContent = customer.name;
        kasbonLoading.classList.remove('hidden');
        kasbonEmpty.classList.add('hidden');
        kasbonItems.innerHTML = '';

        kasbonModal.classList.remove('hidden');
        kasbonModal.classList.add('flex');

        fetch("{{ url('admin/customers') }}/" + customer.id + "/kasbon", {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            kasbonLoading.classList.add('hidden');
            if (!data || data.length === 0) {
                kasbonEmpty.classList.remove('hidden');
                return;
            }
            kasbonEmpty.classList.add('hidden');
            kasbonItems.innerHTML = '';

            data.forEach(item => {
                const createdAt = item.created_at ?? '';
                const div = document.createElement('div');
                div.className = 'border border-slate-100 rounded-2xl px-4 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2';

                div.innerHTML = `
                    <div class="text-sm">
                        <div class="font-semibold text-slate-800">Transaksi #${item.id}</div>
                        <div class="text-xs text-slate-500">${createdAt}</div>
                        <div class="mt-1 text-xs">
                            Total: <span class="font-semibold">${formatRupiah(item.total_amount)}</span><br>
                            Dibayar: <span>${formatRupiah(item.paid_amount)}</span><br>
                            Sisa: <span class="font-semibold text-red-600">${formatRupiah(item.remaining)}</span>
                        </div>
                    </div>
                    <div class="flex md:flex-col gap-2 md:items-end">
                        <button type="button"
                                class="btn-open-pay px-4 py-1.5 rounded-full text-xs font-semibold text-white shadow-sm"
                                data-sale-id="${item.id}"
                                data-remaining="${item.remaining}"
                                data-total="${item.total_amount}"
                                data-paid="${item.paid_amount}"
                                style="background-color: {{ $primaryBlue }};">
                            Bayar
                        </button>
                    </div>
                `;
                kasbonItems.appendChild(div);
            });

            kasbonItems.querySelectorAll('.btn-open-pay').forEach(btn => {
                btn.addEventListener('click', function () {
                    const saleId   = this.dataset.saleId;
                    const remaining = Number(this.dataset.remaining);
                    const total = Number(this.dataset.total);
                    const paid  = Number(this.dataset.paid);
                    openPayKasbonModal(saleId, total, paid, remaining, kasbonCustomerName.textContent);
                });
            });
        })
        .catch(() => {
            kasbonLoading.classList.add('hidden');
            kasbonEmpty.classList.remove('hidden');
            kasbonEmpty.textContent = 'Gagal memuat data kasbon.';
        });
    }

    function closeKasbonModal() {
        kasbonModal.classList.add('hidden');
        kasbonModal.classList.remove('flex');
    }

    if (btnCloseKasbonModal) btnCloseKasbonModal.addEventListener('click', closeKasbonModal);

    document.querySelectorAll('.btn-kasbon').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (this.disabled) return;
            const row = this.closest('.customer-row');
            openKasbonModal(row);
        });
    });

    // ==== MODAL BAYAR KASBON ====
    const payKasbonModal = document.getElementById('payKasbonModal');
    const btnClosePayKasbon = document.getElementById('btnClosePayKasbon');
    const btnCancelPayKasbon = document.getElementById('btnCancelPayKasbon');
    const payKasbonInfo = document.getElementById('payKasbonInfo');
    const paySaleIdInput = document.getElementById('paySaleId');
    const payRemainingRaw = document.getElementById('payRemainingRaw');
    const payRemainingLabel = document.getElementById('payRemainingLabel');
    const payAmountInput = document.getElementById('payAmount');
    const payKasbonError = document.getElementById('payKasbonError');
    const payKasbonForm = document.getElementById('payKasbonForm');

    function openPayKasbonModal(saleId, total, paid, remaining, customerName) {
        paySaleIdInput.value = saleId;
        payRemainingRaw.value = remaining;
        payAmountInput.value = remaining;
        payRemainingLabel.textContent = formatRupiah(remaining);
        payKasbonInfo.textContent = `Pelanggan: ${customerName} • Total ${formatRupiah(total)} • Sudah dibayar ${formatRupiah(paid)}`;
        payKasbonError.classList.add('hidden');
        payKasbonError.textContent = '';

        payKasbonModal.classList.remove('hidden');
        payKasbonModal.classList.add('flex');
        payAmountInput.focus();
    }

    function closePayKasbonModal() {
        payKasbonModal.classList.add('hidden');
        payKasbonModal.classList.remove('flex');
    }

    if (btnClosePayKasbon) btnClosePayKasbon.addEventListener('click', closePayKasbonModal);
    if (btnCancelPayKasbon) btnCancelPayKasbon.addEventListener('click', closePayKasbonModal);

    if (payKasbonForm) {
        payKasbonForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const saleId = paySaleIdInput.value;
            const remaining = Number(payRemainingRaw.value);
            const amount = Number(payAmountInput.value || 0);

            payKasbonError.classList.add('hidden');
            payKasbonError.textContent = '';

            if (amount <= 0) {
                payKasbonError.textContent = 'Nominal bayar harus lebih dari 0.';
                payKasbonError.classList.remove('hidden');
                return;
            }
            if (amount > remaining) {
                payKasbonError.textContent = 'Nominal tidak boleh melebihi sisa kasbon.';
                payKasbonError.classList.remove('hidden');
                return;
            }

            fetch("{{ url('admin/kasbon') }}/" + saleId + "/pay", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ amount: amount })
            })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (res.ok) {
                    closePayKasbonModal();
                    closeKasbonModal();
                    window.location.reload();
                } else if (res.status === 422 && data.errors) {
                    const msg = (data.errors.amount && data.errors.amount[0])
                        || (data.errors.sale && data.errors.sale[0])
                        || 'Gagal menyimpan pembayaran.';
                    payKasbonError.textContent = msg;
                    payKasbonError.classList.remove('hidden');
                } else {
                    payKasbonError.textContent = data.message || 'Gagal menyimpan pembayaran.';
                    payKasbonError.classList.remove('hidden');
                }
            })
            .catch(() => {
                payKasbonError.textContent = 'Terjadi kesalahan jaringan.';
                payKasbonError.classList.remove('hidden');
            });
        });
    }
});
</script>
@endpush
