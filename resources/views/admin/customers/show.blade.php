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

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 mb-6 p-5 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-lg font-bold"
                     style="background-color: rgba(87,160,211,0.12); color:#1f2933;">
                    {{ strtoupper(mb_substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-lg font-semibold text-slate-800">{{ $customer->name }}</div>
                    <div class="text-xs text-slate-500">
                        @if($customer->phone)
                            {{ $customer->phone }} •
                        @endif
                        @if($customer->email)
                            {{ $customer->email }}
                        @endif
                    </div>
                    @if($customer->company)
                        <div class="text-xs text-slate-500 mt-1">
                            {{ $customer->company }}
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

        @if($customer->address)
            <div class="mt-4 text-xs text-slate-600">
                <span class="font-semibold">Alamat:</span> {{ $customer->address }}
            </div>
        @endif

        @if($customer->note)
            <div class="mt-2 text-xs text-slate-600">
                <span class="font-semibold">Catatan:</span> {{ $customer->note }}
            </div>
        @endif
    </div>

    {{-- Riwayat transaksi --}}
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
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
