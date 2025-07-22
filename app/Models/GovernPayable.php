<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernPayable extends Model
{
    use HasFactory;

    protected $table = 'govern_payables';


    protected $fillable = [
        'id',
        'order_id',
        'po_number',
        'name',
        'title',
        'total',
        'payment_date',
        'process_date',
        'payment',
        'payment_method',
        'payment_balance',
        'payment_status',
        'document_status',
        'remarks',
        'status',
        'added_by',
        'updated_by'
    ];
}
