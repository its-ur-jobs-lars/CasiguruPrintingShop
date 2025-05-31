<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Category extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('category', function (Blueprint $table) {
            $table->id();
            $table->string('category_id')->unique();
            $table->string('category_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('isActive')->default(1);
            $table->string('added_by')->nullable(); // Column to store the user who added the category
            $table->string('updated_by')->nullable(); // Column to store the user who updated the category
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
          Schema::dropIfExists('category');
    }
}
