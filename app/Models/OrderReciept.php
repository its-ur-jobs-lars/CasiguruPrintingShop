<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReciept extends Model
{
    use HasFactory;

    protected $table = 'order_receipts';

    protected $fillable = [
        'jo_number',
        'date',
        'name',
        'contact_no',
        'address',
        'total',
        'downpayment',
        'balance',
        'remarks',
        'is_conforme_signed',
        'is_received_signed',
    ];
}
