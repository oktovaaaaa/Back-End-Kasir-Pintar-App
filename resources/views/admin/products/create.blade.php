{{-- resources/views/admin/products/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Produk - Kasir Resto')
@section('page-title', 'Tambah Produk')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Tambah Produk Baru</h2>
                <p class="text-xs text-gray-500">Produk yang dibuat di sini juga akan muncul di aplikasi kasir.</p>
            </div>
            <a href="{{ route('admin.products.index') }}"
               class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white px-4 py-1.5 text-xs text-gray-600 hover:bg-gray-50">
                <i class='bx bx-left-arrow-alt text-base'></i>
                <span>Kembali</span>
            </a>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
              class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            @csrf

            <div>
                <label class="text-xs font-semibold text-gray-700">Nama Produk</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Contoh: Nasi Goreng Spesial"
                       class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]"
                       required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-700">Kategori</label>
                    <select name="category_id"
                            class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]">
                        <option value="">Tanpa kategori</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700">Stok</label>
                    <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}"
                           class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-700">Harga Modal</label>
                    <input type="number" name="cost_price" min="0" value="{{ old('cost_price') }}"
                           placeholder="Contoh: 10000"
                           class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]"
                           required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700">Harga Jual</label>
                    <input type="number" name="price" min="0" value="{{ old('price') }}"
                           placeholder="Contoh: 15000"
                           class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]"
                           required>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-700">Keterangan (opsional)</label>
                <textarea name="description" rows="3"
                          placeholder="Contoh: Porsi besar, bisa level pedas."
                          class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-700">Foto Produk (opsional)</label>
                <input type="file" name="image"
                       class="mt-1 block w-full text-xs text-gray-600 file:mr-4 file:rounded-full file:border-0 file:bg-[#57A0D3] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-sky-600">
                <p class="mt-1 text-[10px] text-gray-400">Maks 2 MB, format: JPG, PNG.</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.products.index') }}"
                   class="inline-flex items-center rounded-full border border-gray-200 px-5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center rounded-full bg-[#57A0D3] px-6 py-1.5 text-xs font-semibold text-white hover:bg-sky-600">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
@endsection
