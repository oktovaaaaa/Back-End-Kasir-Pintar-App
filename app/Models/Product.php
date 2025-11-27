<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // PASTIKAN kolom-kolom ini ada SEMUA
    protected $fillable = [
        'name',
        'category_id',
        'price',        // <— HARGA JUAL
        'cost_price',   // <— HARGA MODAL
        'stock',
        'description',
        'image_path',
    ];

    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // === TAMBAHAN: relasi ke sale_items ===
    public function saleItems()
    {
        return $this->hasMany(\App\Models\SaleItem::class);
    }

    // agar Flutter dapat URL lengkap untuk foto
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        return asset('storage/' . $this->image_path);
    }
}
