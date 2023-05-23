<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('service_staff_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->date('date');
            $table->string('time_slot_id');
            $table->string('address');
            $table->string('status');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('price');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_appointments');
    }
};
