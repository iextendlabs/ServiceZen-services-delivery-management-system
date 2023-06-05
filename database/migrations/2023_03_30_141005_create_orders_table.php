<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->decimal('total_amount', 8, 2);
            $table->string('payment_method');
            $table->string('status')->nullable();
            $table->string('affiliate_id')->nullable();
            $table->string('buildingName')->nullable();
            $table->string('area')->nullable();
            $table->string('landmark');
            $table->string('flatVilla')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('number')->nullable();
            $table->string('whatsapp');
            $table->string('service_staff_id');
            $table->string('date');
            $table->string('time_slot_id');
            $table->string('latitude');
            $table->string('longitude');
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
        Schema::dropIfExists('orders');
    }
};
