<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Sales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Order reference
            $table->string('sales_id')->nullable(); // Link to payment record
            $table->string('order_id'); // e.g., 0000236
            $table->string('jo_number')->nullable(); // Job Order number

            // Customer Info
            $table->string('name');
            $table->string('contact_no')->nullable();
            $table->string('address')->nullable();

            // Item Info
            $table->string('category_id');
            $table->string('subcategory_id')->nullable();
            $table->integer('qty');
            $table->decimal('price', 10, 2);
            $table->decimal('amount', 10, 2); // qty * price

            // Financial Summary
            $table->decimal('total', 10, 2);     // with layout fee, discount, etc.
            $table->decimal('payment', 10, 2);   // total payment made
            $table->decimal('balance', 10, 2);   // should be 0.00

            // Tracking
            $table->string('payment_status')->nullable(); // Paid
            $table->date('completed_at')->nullable();     // Date marked as fully paid
            $table->string('added_by')->nullable();       // user who confirmed sale

            // Flags
            $table->boolean('isActive')->default(true);   // soft delete
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
         Schema::dropIfExists('sales');
    }
}
