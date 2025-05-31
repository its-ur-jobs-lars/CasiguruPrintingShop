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
        'price_10_50',
        'price_51_100',
        'price_101_500',
        'remarks',
        'isActive',
        'added_by',
        'updated_by'
    ];
}
