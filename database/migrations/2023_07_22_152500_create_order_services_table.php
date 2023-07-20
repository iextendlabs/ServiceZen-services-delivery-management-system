<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('service_name')->nullable();
            $table->string('status');
            $table->string('price');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('CASCADE');

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('SET NULL');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_services');
    }
};
