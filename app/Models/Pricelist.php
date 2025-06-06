<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricelist extends Model
{
    use HasFactory;

    protected $table = 'pricelistItem'; // Specify the table name

    protected $fillable = [
        'pricelist_id',
        'category_id',
        'subcategory_id',
        'price_1',
        'price_2_50',
        'price_51_100',
        'price_101_500',
        'price_501_999',
        'price_1000_up',
        'remarks',
        'isActive',
        'added_by',
        'updated_by'
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
