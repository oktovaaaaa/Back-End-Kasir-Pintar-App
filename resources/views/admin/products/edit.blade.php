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

        {{-- FORM UPDATE PRODUK --}}
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

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.products.index') }}"
                   class="inline-flex items-center rounded-full border border-gray-200 px-5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center rounded-full bg-[#57A0D3] px-6 py-1.5 text-xs font-semibold text-white hover:bg-sky-600">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        {{-- HAPUS PRODUK (FORM TERPISAH) --}}
        <div class="rounded-2xl border border-red-100 bg-white p-4 shadow-sm mt-3">
            <div class="flex justify-between items-center border-t border-dashed border-red-200 pt-3 mt-1">
                <p class="text-[11px] text-gray-500">
                    Butuh menghapus produk ini? Tindakan ini tidak bisa dibatalkan.
                </p>

                <form id="deleteProductForm"
                      action="{{ route('admin.products.destroy', $product) }}"
                      method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            id="deleteProductButton"
                            class="inline-flex items-center rounded-full bg-red-50 px-4 py-1.5 text-[11px] font-semibold text-red-600 hover:bg-red-100">
                        <i class='bx bx-trash text-sm mr-1'></i>
                        Hapus Produk
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS PRODUK --}}
    <div id="deleteConfirmModal"
         class="fixed inset-0 z-50 hidden bg-black/40 items-center justify-center">
        <div class="w-full max-w-sm mx-4 rounded-2xl bg-white p-5 shadow-xl">
            <div class="flex items-start gap-3">
                <div class="mt-1 flex h-9 w-9 items-center justify-center rounded-full bg-red-50">
                    <i class='bx bx-error text-lg text-red-500'></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-gray-800">
                        Hapus produk ini?
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">
                        Produk <span class="font-semibold text-gray-700">{{ $product->name }}</span> akan dihapus dari daftar.
                        Jika produk sudah pernah dipakai di transaksi, riwayat transaksi tetap ada,
                        tetapi produk ini tidak bisa digunakan lagi untuk penjualan baru.
                    </p>
                </div>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button"
                        id="cancelDeleteBtn"
                        class="inline-flex items-center rounded-full border border-gray-200 px-4 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                    Batal
                </button>
                <button type="button"
                        id="confirmDeleteBtn"
                        class="inline-flex items-center rounded-full bg-red-500 px-4 py-1.5 text-xs font-semibold text-white hover:bg-red-600">
                    Ya, hapus
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const deleteForm   = document.getElementById('deleteProductForm');
        const triggerBtn   = document.getElementById('deleteProductButton');
        const modal        = document.getElementById('deleteConfirmModal');
        const cancelBtn    = document.getElementById('cancelDeleteBtn');
        const confirmBtn   = document.getElementById('confirmDeleteBtn');

        if (!deleteForm || !triggerBtn || !modal) return;

        const openModal = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        triggerBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        });

        cancelBtn?.addEventListener('click', () => {
            closeModal();
        });

        // klik area gelap di luar card = tutup modal
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });

        confirmBtn?.addEventListener('click', () => {
            deleteForm.submit();
        });
    });
</script>
@endpush
