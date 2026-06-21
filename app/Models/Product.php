<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'specification',
        'unit',
        'default_price',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
    ];

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
                    ->withPivot('supplier_price', 'status_supplier')
                    ->withTimestamps()
                    ->orderByPivot('status_supplier', 'desc');
    }

    public function primarySupplier()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier')
                    ->withPivot('supplier_price', 'status_supplier')
                    ->wherePivot('status_supplier', true)
                    ->limit(1);
    }

    // TAMBAHAN: Untuk melihat item produk ini sudah dipesan di PO mana saja
    public function poItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'product_id');
    }
}
