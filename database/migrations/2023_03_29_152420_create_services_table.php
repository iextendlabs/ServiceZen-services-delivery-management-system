<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('short_description');
            $table->string('price');
            $table->string('duration')->nullable();
            $table->string('image');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('discount')->nullable();
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on('service_categories')
                ->onDelete('SET NULL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
};
