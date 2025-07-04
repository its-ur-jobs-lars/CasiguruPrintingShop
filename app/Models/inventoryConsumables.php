<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventoryConsumables extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'category_id',
        'subcategory_id',
        'unit',
        'quantity',
        'minimum_stock',
        'purchase_price',
        'selling_price',
        'supplier_id',
        'expiration_date',
        'remarks',
        'isActive',
        'added_by',
    ];

public function category()
{
    return $this->belongsTo(Category::class, 'category_id', 'category_id');
}

public function subcategory()
{
    return $this->belongsTo(SubCategory::class, 'subcategory_id', 'subcategory_id', 'image');
}
}
