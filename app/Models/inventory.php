<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventory extends Model
{
    use HasFactory;

    protected $table = 'inventoryconsumables';

    protected $fillable = [
        'inventory_id',
        'item_name',
        'category_id',
        'subcategory_id',
        'unit',
        'quantity',
        'minimum_stock',
        'purchase_price',
        'selling_price',
        'location',
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
