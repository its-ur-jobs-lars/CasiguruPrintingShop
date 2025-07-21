<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EmployeeBenefits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number'); // Use string if you're using something like EMP-00123
            $table->date('date')->nullable();
            $table->string('employee_name');
            $table->decimal('sss', 10, 2)->default(0.00);
            $table->string('sss_number')->nullable();
            $table->decimal('pagibig', 10, 2)->default(0.00);
            $table->string('pagibig_number')->nullable();
            $table->decimal('philhealth', 10, 2)->default(0.00);
            $table->string('philhealth_number')->nullable();
            $table->timestamps();
            $table->string('added_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->boolean('isActive')->default(true);

});
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_benefits');
    }
}
