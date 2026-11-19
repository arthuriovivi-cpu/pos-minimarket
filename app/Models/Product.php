<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'sku', 'name', 'description', 'unit',
        'cost_price', 'sell_price', 'stock', 'min_stock', 'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Status stok: habis, hampir_habis, menipis, atau aman.
     * Dipakai di halaman inventaris dan dashboard.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'habis';
        }
        if ($this->stock <= (int) floor($this->min_stock / 2)) {
            return 'hampir_habis';
        }
        if ($this->stock <= $this->min_stock) {
            return 'menipis';
        }

        return 'aman';
    }
}