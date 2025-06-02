<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestReceipt extends Model
{
    use HasFactory;

    protected $table = 'order_receipts';

    protected $fillable = [
        'order_receipt_id',
        'order_id',
        'name',
        'contact_no',
        'address',
        'category_id',
        'subcategory_id',
        'qty',
        'price',
        'date',
        'amount',
        'total',
        'payment',
        'balance',
        'jo_number',
        'payment_method',
        'payment_status',
        'reference_number',
        'payment_date',
        'is_conforme_signed',
        'is_received_signed',
        'status',
        'remarks',
        'isActive',
        'service_by',
        'updated_by',
        
    ];
}
