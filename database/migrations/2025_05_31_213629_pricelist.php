<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pricelist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('pricelistItem', function (Blueprint $table) {
            $table->id();
            $table->string('pricelist_id')->unique(); 
            $table->string('category_id')->nullable(); 
            $table->string('subcategory_id')->nullable(); 
            $table->decimal('price_1', 8, 2);    
            $table->decimal('price_2_50', 8, 2);    
            $table->decimal('price_51_100', 8, 2);   
            $table->decimal('price_101_500', 8, 2);  
            $table->decimal('price_501_999', 8, 2);  
            $table->decimal('price_1000_up', 8, 2);  
            $table->string('remarks')->nullable();  
            $table->boolean('isActive')->default(1); // Active status
            $table->string('added_by')->nullable();  // Column to store the user who added the pricelist item
            $table->string('updated_by')->nullable(); // Column to store the user who updated the pricelist item
            $table->timestamps();                    // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists('pricelistItem');
    }
}
