<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InventoryConsumables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('inventoryConsumables', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_id')->unique();
            $table->string('item_name');
            $table->string('category_id')->nullable();
            $table->string('subcategory_id')->nullable();
            $table->string('unit')->default('pcs');
            $table->integer('quantity')->default(0);
            $table->integer('minimum_stock')->default(0)->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->string('supplier_id')->nullable();
            $table->date('expiration_date')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('isActive')->default(true);
            $table->string('added_by')->nullable();
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
        Schema::dropIfExists('inventoryConsumables');
    }
}
