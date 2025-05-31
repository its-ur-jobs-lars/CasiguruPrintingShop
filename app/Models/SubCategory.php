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
}
