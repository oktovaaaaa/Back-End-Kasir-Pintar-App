<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Tampil halaman stok + monitoring + shortcut CRUD
     */
    public function index()
    {
        $products   = Product::with('category')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form tambah produk (admin)
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru dari admin
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name'        => $data['name'],
            'category_id' => $data['category_id'] ?? null,
            'price'       => $data['price'],
            'cost_price'  => $data['cost_price'],
            'stock'       => $data['stock'],
            'description' => $data['description'] ?? null,
            'image_path'  => $imagePath,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Form edit produk
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk dari admin
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // hapus foto lama kalau ada
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->image_path = $request->file('image')->store('products', 'public');
        }

        $product->name        = $data['name'];
        $product->category_id = $data['category_id'] ?? null;
        $product->price       = $data['price'];
        $product->cost_price  = $data['cost_price'];
        $product->stock       = $data['stock'];
        $product->description = $data['description'] ?? null;
        $product->save();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus produk
     */
    public function destroy(Product $product)
    {
        try {
            // hapus file gambar kalau ada
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            // delete produk
            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus.');
        } catch (QueryException $e) {
            return back()->with('error', 'Produk tidak dapat dihapus karena masih terhubung dengan data lain.');
        }
    }
}
