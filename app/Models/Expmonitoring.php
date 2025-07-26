<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expmonitoring extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'date',
        'si_or_no',
        'supplier_id',
        'particular',
        'amount',
        'qty',
        'added_by', 
        'updated_by',
        'isActive',
        'subtotal',
        'remarks',
    ];

    /**
     * Get the supplier associated with the expense.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
