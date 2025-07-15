<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Orders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Customer Info
            $table->string('order_id'); // e.g., 0000236
            $table->string('name');
            $table->string('contact_no')->nullable();
            $table->string('address')->nullable();

            // Order Item Info
            $table->string('category_id');
            $table->string('subcategory_id')->nullable();
            $table->integer('qty');
            $table->decimal('price', 10, 2);
            $table->decimal('amount', 10, 2); // qty × price
             $table->decimal('layout_fee', 10, 2); // qty × price

            // Financial Summary
            $table->decimal('total', 10, 2)->default(0.00);
            $table->string('jo_number')->nullable(); // Job Order Number
            $table->date('deadline')->nullable();
            $table->string('status')->nullable();
            // Status of the order
             $table->string('customer_type')->nullable();

            // Optional Notes
            $table->text('remarks')->nullable();
             $table->boolean('isActive')->default(1);
             $table->boolean('inventory_deducted')->default(false);
            $table->string('added_by')->nullable();  // Column to store the user who added the pricelist item
            $table->string('updated_by')->nullable();
             

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
        Schema::dropIfExists('orders');
    }
}
