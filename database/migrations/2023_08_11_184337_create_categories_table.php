<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('category_row_id');
            $table->string('category_name');
            $table->string('category_image')->nullable();
            $table->text('category_description')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('has_child')->nullable();
            $table->tinyInteger('is_featured')->default(0);
            $table->integer('level')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
