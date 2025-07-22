<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GovernPayableModal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('govern_payables', function (Blueprint $table) {
        $table->id('id'); // Primary key
        $table->string('order_id')->unique();
        $table->string('po_number')->nullable();
        $table->string('name');
        $table->date('payment_date')->nullable(); // Optional date for payment
        $table->string('process_date')->nullable(); // e.g., 'pending', '
        $table->string('title')->nullable();
        $table->decimal('total', 10, 2)->default(0.00);
        $table->decimal('payment', 10, 2)->default(0.00); // Total payment amount
        $table->string('payment_method')->nullable(); // e.g., 'cash', 'cheque', 'gcash'
        $table->decimal('payment_balance', 10, 2)->default(0.00);
        $table->string('payment_status')->nullable();; // 'paid', 'partial', 'unpaid'
        $table->text('remarks')->nullable();
        $table->string('document_status')->nullable();
        $table->string('status')->nullable();; // 'pending', 'completed', etc.
        $table->string('added_by')->nullable();
        $table->string('updated_by')->nullable();
        $table->boolean('isActive')->default(true);
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
        Schema::dropIfExists('govern_payables');
    }
}
