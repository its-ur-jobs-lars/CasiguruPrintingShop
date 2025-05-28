<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

      protected $table = 'EmployeeInformation';

    protected $fillable = [
        'employee_id',
        'emp_Lastname',
        'emp_Firstname',
        'emp_Middlename',
        'ext_name',
        'employee_number',
        'task',
        'date_Hired',
        'description',
        'isActive',
        'added_by',
        'updated_by',

    ];
}
