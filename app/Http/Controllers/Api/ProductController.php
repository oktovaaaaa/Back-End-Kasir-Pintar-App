<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // GET /api/products
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }

    // POST /api/products
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price'       => 'required|numeric|min:0',      // harga jual
            'cost_price'  => 'required|numeric|min:0',      // harga modal
            'stock'       => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name'        => $data['name'],
            'category_id' => $data['category_id'] ?? null,
            'price'       => $data['price'],
            'cost_price'  => $data['cost_price'],
            'stock'       => $data['stock'],
            'description' => $data['description'] ?? null,
            'image_path'  => $imagePath,
        ]);

        return response()->json($product, 201);
    }

    // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }

    // PUT /api/products/{id}
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'price'       => 'required|numeric|min:0',      // harga jual
            'cost_price'  => 'required|numeric|min:0',      // harga modal
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

        return response()->json($product);
    }

    // DELETE /api/products/{id}
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}
