<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeActivityLogs extends Model
{
    use HasFactory;

     protected $table = 'EmployeeActivity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'description',
        'ip_address',
        'isActive',
    ];
}
