<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_id',
        'name',
        'contact_no',
        'address',
        'contact_no',
        'category_id',
        'subcategory_id',
        'qty',
        'price',
        'amount',
        'email',
        'layout_fee', // Added layout fee
        'total',
        'jo_number',
        'deadline',
        'customer_type', // Added customer type
        'status',
        'remarks',
        'isActive',
        'added_by',
        'updated_by',
    ];



public function subcategory()
{
    return $this->belongsTo(SubCategory::class, 'subcategory_id', 'subcategory_id');
}

public function Payment()
{
    return $this->hasOne(payment::class, 'order_id', 'order_id')
        ->whereColumn('subcategory_id', 'subcategory_id')
        ->latest('payment_date')
        ->latest('id');
}





}
