{{-- resources/views/admin/customers/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail Pelanggan - Kasir Resto')
@section('page-title', 'Detail Pelanggan')

@section('content')
    @php $primaryBlue = '#57A0D3'; @endphp

    <div class="mb-4">
        <a href="{{ route('admin.customers.index') }}"
           class="inline-flex items-center text-xs text-slate-500 hover:text-slate-700 mb-2">
            <i class="bx bx-arrow-back mr-1 text-sm"></i> Kembali ke daftar pelanggan
        </a>
    </div>

    {{-- PANEL PROFIL --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 mb-6 p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-lg font-bold"
                     style="background-color: rgba(87,160,211,0.12); color:#1f2933;">
                    {{ strtoupper(mb_substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-lg font-semibold text-slate-800">{{ $customer->name }}</div>
                    <div class="text-xs text-slate-500 flex flex-wrap gap-1 items-center">
                        @if($customer->phone)
                            <span>{{ $customer->phone }}</span>
                        @endif
                        @if($customer->email)
                            @if($customer->phone) <span>•</span> @endif
                            <span>{{ $customer->email }}</span>
                        @endif
                    </div>
                    @if($customer->company)
                        <div class="text-xs text-slate-500 mt-1">
                            Instansi/Perusahaan: <span class="font-semibold">{{ $customer->company }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col items-start md:items-end gap-2 text-sm">
                <div class="text-xs text-slate-500">Total utang aktif</div>
                @if($totalDebt > 0)
                    <div class="text-base font-semibold text-red-600">
                        Rp {{ number_format($totalDebt, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-red-500">
                        Masih ada kasbon yang belum lunas.
                    </div>
                @else
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        Tidak ada utang
                    </div>
                @endif
            </div>
        </div>

        {{-- Detail tambahan --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-slate-600">
            <div>
                <span class="font-semibold">Alamat:</span>
                <span>{{ $customer->address ?: '-' }}</span>
            </div>
            <div>
                <span class="font-semibold">Instansi / Perusahaan:</span>
                <span>{{ $customer->company ?: '-' }}</span>
            </div>
            <div class="md:col-span-2">
                <span class="font-semibold">Catatan:</span>
                <span>{{ $customer->note ?: '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Riwayat transaksi + bayar kasbon --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-4 md:p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm md:text-base font-semibold text-slate-800">
                Riwayat Transaksi
            </h3>
            <span class="text-[11px] text-slate-500">
                {{ $customer->sales->count() }} transaksi
            </span>
        </div>

        @if($customer->sales->isEmpty())
            <div class="text-sm text-slate-500 text-center py-6">
                Belum ada transaksi untuk pelanggan ini.
            </div>
        @else
            <div class="space-y-3 text-sm">
                @foreach($customer->sales as $sale)
                    @php
                        $isKasbon = $sale->status === 'kasbon';
                        $remaining = max(0, $sale->total_amount - $sale->paid_amount);
                    @endphp
                    <div class="border border-slate-100 rounded-2xl px-4 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-400">#{{ $sale->id }}</span>
                                <span class="font-semibold text-slate-800">
                                    Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500">
                                {{ $sale->created_at->format('d M Y • H:i') }}
                            </div>
                            @if($isKasbon)
                                <div class="text-[11px] mt-1">
                                    Dibayar: Rp {{ number_format($sale->paid_amount, 0, ',', '.') }} •
                                    Sisa: <span class="font-semibold text-red-600">
                                        Rp {{ number_format($remaining, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 md:flex-col md:items-end">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold
                                         {{ $isKasbon ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                             {{ $isKasbon ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                {{ $isKasbon ? 'Kasbon' : 'Lunas' }}
                            </span>

                            @if($isKasbon && $remaining > 0)
                                <button type="button"
                                        class="btn-open-pay px-3 py-1.5 rounded-full text-xs font-semibold text-white shadow-sm"
                                        data-sale-id="{{ $sale->id }}"
                                        data-total="{{ $sale->total_amount }}"
                                        data-paid="{{ $sale->paid_amount }}"
                                        data-remaining="{{ $remaining }}"
                                        style="background-color: {{ $primaryBlue }};">
                                    Bayar
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- MODAL BAYAR KASBON (sama seperti index) --}}
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
                        Nilai awal otomatis sesuai sisa kasbon, tapi bisa dikurangi.
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

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function openPayKasbonModal(saleId, total, paid, remaining) {
        paySaleIdInput.value = saleId;
        payRemainingRaw.value = remaining;
        payAmountInput.value = remaining;
        payRemainingLabel.textContent = formatRupiah(remaining);
        payKasbonInfo.textContent = `Pelanggan: {{ $customer->name }} • Total ${formatRupiah(total)} • Sudah dibayar ${formatRupiah(paid)}`;
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

    document.querySelectorAll('.btn-open-pay').forEach(btn => {
        btn.addEventListener('click', function () {
            const saleId = this.dataset.saleId;
            const total = Number(this.dataset.total);
            const paid = Number(this.dataset.paid);
            const remaining = Number(this.dataset.remaining);
            openPayKasbonModal(saleId, total, paid, remaining);
        });
    });

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
