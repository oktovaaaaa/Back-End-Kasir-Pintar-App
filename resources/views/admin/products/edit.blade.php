{{-- resources/views/admin/products/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Produk - Kasir Resto')
@section('page-title', 'Edit Produk')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Edit Produk</h2>
                <p class="text-xs text-gray-500">Perbarui atau hapus data produk yang sudah ada.</p>
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

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
              class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-4">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}"
                         alt="{{ $product->name }}"
                         class="h-16 w-16 rounded-xl object-cover">
                @else
                    <div class="h-16 w-16 rounded-xl bg-[#57A0D3]/10 flex items-center justify-center">
                        <i class='bx bx-image-alt text-2xl text-[#57A0D3]'></i>
                    </div>
                @endif
                <div class="text-xs text-gray-500">
                    <p class="font-semibold text-gray-700">Foto Produk</p>
                    <p>Jika tidak diganti, biarkan input foto kosong.</p>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-700">Nama Produk</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}"
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
                            <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700">Stok</label>
                    <input type="number" name="stock" min="0" value="{{ old('stock', $product->stock) }}"
                           class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-700">Harga Modal</label>
                    <input type="number" name="cost_price" min="0" value="{{ old('cost_price', $product->cost_price) }}"
                           class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]"
                           required>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700">Harga Jual</label>
                    <input type="number" name="price" min="0" value="{{ old('price', $product->price) }}"
                           class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]"
                           required>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-700">Keterangan (opsional)</label>
                <textarea name="description" rows="3"
                          class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-800 focus:border-[#57A0D3] focus:ring-[#57A0D3]">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-700">Ganti Foto Produk (opsional)</label>
                <input type="file" name="image"
                       class="mt-1 block w-full text-xs text-gray-600 file:mr-4 file:rounded-full file:border-0 file:bg-[#57A0D3] file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-white hover:file:bg-sky-600">
                <p class="mt-1 text-[10px] text-gray-400">Maks 2 MB, format: JPG, PNG.</p>
            </div>

            <div class="flex flex-col gap-3 pt-2">
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.products.index') }}"
                       class="inline-flex items-center rounded-full border border-gray-200 px-5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center rounded-full bg-[#57A0D3] px-6 py-1.5 text-xs font-semibold text-white hover:bg-sky-600">
                        Simpan Perubahan
                    </button>
                </div>

                {{-- HAPUS PRODUK --}}
                <div class="flex justify-between items-center border-t border-dashed border-red-200 pt-3 mt-1">
                    <p class="text-[11px] text-gray-500">Butuh menghapus produk ini?</p>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                          onsubmit="return confirm('Yakin menghapus {{ $product->name }} ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center rounded-full bg-red-50 px-4 py-1.5 text-[11px] font-semibold text-red-600 hover:bg-red-100">
                            <i class='bx bx-trash text-sm mr-1'></i>
                            Hapus Produk
                        </button>
                    </form>
                </div>
            </div>
        </form>
    </div>
@endsection
