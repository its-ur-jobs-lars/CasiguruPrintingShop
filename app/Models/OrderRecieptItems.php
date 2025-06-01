<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRecieptItems extends Model
{
    use HasFactory;

    protected $table = 'order_receipt_items';

    protected $fillable = [
        'order_receipt_id',
        'qty',
        'description',
        'unit_price',
        'amount',
    ];
    public function orderReceipt()
    {
        return $this->belongsTo(OrderReciept::class, 'order_receipt_id');
    }
}
