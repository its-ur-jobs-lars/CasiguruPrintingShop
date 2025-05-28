<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Employee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        schema::create('EmployeeInformation', function (Blueprint $table) {
        $table->id();
        $table->string('employee_id')->unique();
        $table->string('emp_Lastname')->nullable();
        $table->string('emp_Firstname')->nullable();
        $table->string('emp_Middlename')->nullable();
        $table->string('ext_name')->nullable();
        $table->string('task');
        $table->date('date_Hired');
        $table->text('description')->nullable();
        $table->string('employee_number')->nullable();
        $table->boolean('isActive')->default(1);
         $table->string('added_by')->nullable(); // Column to store the user who added the employee
         $table->string('updated_by')->nullable(); // Column to store the user who updated the employee
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
           Schema::dropIfExists('EmployeeInformation');
    }
}
