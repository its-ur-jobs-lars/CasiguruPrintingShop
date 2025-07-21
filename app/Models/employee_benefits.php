<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employee_benefits extends Model
{
    use HasFactory;

    protected $table = 'employee_benefits';

    protected $fillable = [
        'employee_number',
        'date',
        'employee_name',
        'sss',
        'sss_number',
        'pagibig',
        'pagibig_number',
        'philhealth',
        'philhealth_number',
        'added_by',
        'updated_by',
        'isActive',
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'employee_number', 'employee_number');
}
}
