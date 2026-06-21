<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'pic_name',
    ];

    // TAMBAHAN: Relasi Many-to-Many terbalik ke Product melalui pivot
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_supplier')
                    ->withPivot('supplier_price', 'status_supplier')
                    ->withTimestamps();
    }

    // TAMBAHAN: Untuk melihat daftar semua lembar dokumen PO yang pernah dikirim ke supplier ini
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }
}
