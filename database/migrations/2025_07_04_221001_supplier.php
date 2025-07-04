<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Supplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id')->unique(); // Unique identifier for the supplier
            $table->string('category_id')->nullable(); // Optional category ID for supplier classification
            $table->string('subcategory_id')->nullable(); // Optional subcategory ID for more specific
            $table->string('name'); // Supplier name
            $table->string('contact_person')->nullable(); // Optional contact name
            $table->string('contact_number')->nullable(); // Phone number
            $table->string('address')->nullable(); // Supplier address
            $table->text('remarks')->nullable(); // Notes or special terms
            $table->string('added_by')->nullable(); // User who added the supplier
            $table->string('updated_by')->nullable(); // User who last updated the supplier
            $table->boolean('is_active')->default(true); // Whether supplier is active
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
        schema::dropIfExists('suppliers');
    }
}
