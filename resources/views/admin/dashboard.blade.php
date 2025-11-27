@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

        {{-- Card 1 --}}
        <div class="p-6 bg-white rounded-xl shadow border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Total Produk</h3>
            <p class="text-3xl font-bold text-[#57A0D3] mt-2">120</p>
        </div>

        {{-- Card 2 --}}
        <div class="p-6 bg-white rounded-xl shadow border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Total Kasir</h3>
            <p class="text-3xl font-bold text-[#57A0D3] mt-2">15</p>
        </div>

        {{-- Card 3 --}}
        <div class="p-6 bg-white rounded-xl shadow border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700">Total Pelanggan</h3>
            <p class="text-3xl font-bold text-[#57A0D3] mt-2">38</p>
        </div>

    </div>

    {{-- Placeholder Section --}}
    <div class="mt-10 p-6 bg-white rounded-xl shadow border border-gray-200">
        <h2 class="text-xl font-semibold mb-3 text-gray-700">
            Selamat datang di Dashboard Admin
        </h2>
        <p class="text-gray-600">
            Ini adalah tampilan awal dashboard. Nanti bisa kamu isi dengan chart, laporan, atau statistik lainnya.
        </p>
    </div>
@endsection
