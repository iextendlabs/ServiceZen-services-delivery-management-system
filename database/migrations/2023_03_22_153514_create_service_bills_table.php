<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('service_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_appointment_id');
            $table->float('amount');
            $table->timestamps();

            $table->foreign('service_appointment_id')->references('id')->on('service_appointments');
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_bills');
    }
};
