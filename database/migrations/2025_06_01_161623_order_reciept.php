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
            $table->string('jo_number')->unique(); // e.g., 0000236
            $table->date('date')->nullable();

            // Customer information
            $table->string('name');
            $table->string('contact_no')->nullable();
            $table->string('address')->nullable();

            // Financials
            $table->decimal('total', 10, 2)->default(0.00);
            $table->decimal('downpayment', 10, 2)->default(0.00);
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->text('remarks')->nullable();

            // Signatures
            $table->boolean('is_conforme_signed')->default(false);
            $table->boolean('is_received_signed')->default(false);

            $table->timestamps();
        });

        Schema::create('order_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_receipt_id')->constrained()->onDelete('cascade');
            $table->integer('qty');
            $table->string('description');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('amount', 10, 2);
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
         Schema::dropIfExists('order_receipt_items');
        Schema::dropIfExists('order_receipts');
    }
}
