<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment extends Model
{

    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'payment_id',
        'order_id',
        'jo_number',
        'name',
        'address',
        'amount',
        'balance',
        'total',
        'payment',
        'payment_method',
        'reference_number',
        'payment_date',
        'payment_status',
        'remarks',
        'isActive',
        'service_by',
        'updated_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'total' => 'decimal:2',
        'isActive' => 'boolean',
    ];
}

