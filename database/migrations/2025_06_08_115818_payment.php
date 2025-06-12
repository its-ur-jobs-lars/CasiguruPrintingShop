<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Payment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('payment_id')->nullable();
            // Link to order receipt or order
         
            $table->string('order_id')->nullable(); // e.g., JO-0000236
            $table->string('subcategory_id')->nullable(); // e.g., JO-0000236
            $table->string('jo_number')->nullable();
             $table->string('name')->nullable(); // e.g., JO-0000236
            $table->string('address')->nullable();

            // Each payment instance (partial or full)
            $table->decimal('amount', 10, 2)->default(0.00); 
            $table->decimal('payment', 10, 2)->default(0.00); // Actual payment amount
            $table->decimal('balance', 10, 2)->default(0.00); // To be recalculated after payment
            $table->decimal('total', 10, 2)->default(0.00); // Set based on the order or receipt

            // Payment details
            $table->string('payment_method')->nullable();    // e.g., Cash, Bank Transfer
            $table->string('reference_number')->nullable();  // Transaction or check #
            $table->date('payment_date')->nullable();
            $table->string('payment_status')->nullable();    // e.g., Paid, Partial, Unpaid

            $table->text('remarks')->nullable();

            // Metadata
            $table->boolean('isActive')->default(true);
            $table->string('service_by')->nullable();   // who recorded this payment
            $table->string('updated_by')->nullable();

            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}

