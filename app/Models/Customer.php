<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'company', // <-- INI YANG TADI HILANG
        'note',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
