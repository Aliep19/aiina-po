<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'product_code',
        'name',
        'specification',
        'unit',
        'default_price',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
    ];

    /*
    |──────────────────────────────────────────────
    | RELATIONS
    |──────────────────────────────────────────────
    */

    /**
     * Supplier yang terhubung ke produk ini (many-to-many).
     * Pivot menyimpan harga supplier & flag primary.
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
                    ->withPivot('supplier_price', 'is_primary')
                    ->withTimestamps()
                    ->orderByPivot('is_primary', 'desc');
    }

    /**
     * Supplier utama (eager-load ringan: hanya 1 row lewat pivot).
     * Gunakan ini untuk tampil di list agar tidak N+1.
     */
    public function primarySupplier()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
                    ->withPivot('supplier_price', 'is_primary')
                    ->wherePivot('is_primary', true)
                    ->limit(1);
    }
}
