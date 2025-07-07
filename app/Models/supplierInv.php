<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplierInv extends Model
{
    use HasFactory;
    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'supplier_id',
        'category_id',
        'subcategory_id',
        'contact_person',
        'contact_number',
        'address',
        'remarks',
        'isActive',
        'added_by',
    ];

    public function category()
{
    return $this->belongsTo(Category::class, 'category_id');
}

public function subcategory()
{
    return $this->belongsTo(SubCategory::class, 'subcategory_id');
}

}
