<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sales extends Model
{
    use HasFactory;
  
    protected  $table = 'sales';

    protected $fillable = [
        'sales_id',
        'order_id',
        'jo_number',
        'name',
        'contact_no',
        'address',
        'category_id',
        'subcategory_id',
        'qty',
        'price',
        'amount',
        'total',
        'payment',
        'balance',
        'payment_status',
        'completed_at',
        'added_by',
        'isActive'
    ];
}
