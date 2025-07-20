<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $table = 'sub-category'; // Specify the table name
    protected $fillable = [
        'subcategory_id',
        'subcategory_name',
        'category_id',
        'description',
        'image',
        'isActive',
        'added_by',
        'updated_by'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function inventories()
    {
        return $this->hasMany(inventory::class, 'subcategory_id', 'subcategory_id');
    }
}
