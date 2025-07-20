<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderReciept extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('order_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('order_receipt_id')->unique(); // e.g., OR-0000236
            $table->string('order_id')->nullable(); // e.g., JO-0000236
            // e.g., 0000236
            $table->date('date')->nullable();

            // Customer information

            $table->string('name');
            $table->string('contact_no')->nullable();
            $table->string('address')->nullable();

            // Financials
            $table->string('category_id')->nullable();
            $table->string('subcategory_id')->nullable();
            $table->integer('qty')->nullable(); // Quantity of items
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->decimal('payment', 10, 2)->default(0.00);
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->string('jo_number')->nullable();
            $table->string('receipt_number')->nullable();
            $table->decimal('layout_fee', 10, 2); // Layout fee for the order
            $table->string('payment_method')->nullable(); // e.g., Cash, Check, Bank Transfer
            $table->string('reference_number')->nullable(); // e.g., 123456
            $table->date('payment_date')->nullable(); // Date of payment
            $table->string('status')->nullable();
            $table->string('payment_status')->nullable(); // e.g., BPI, BDO
            $table->text('remarks')->nullable();

            // Signatures
            $table->boolean('is_conforme_signed')->default(false);
            $table->boolean('is_received_signed')->default(false);
            $table->boolean('isActive')->default(true);
            $table->string('service_by')->nullable(); // User who added the receipt
            $table->string('updated_by')->nullable(); // User who last updated the receipt

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
        Schema::dropIfExists('order_receipts');
    }
}
