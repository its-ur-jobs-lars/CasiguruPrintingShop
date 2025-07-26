<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Expenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->date('date');
        $table->string('si_or_no')->nullable();
        $table->string('supplier_id');
        $table->text('particular');
        $table->string('qty')->nullable();
        $table->decimal('amount', 12, 2);
        $table->decimal('subtotal', 12, 2)->nullable();
        $table->text('remarks')->nullable();
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
        Schema::dropIfExists('expenses');
    }
}
