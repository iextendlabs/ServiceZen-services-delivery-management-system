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
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('total_amount', 8, 2);
            $table->string('payment_method');
            $table->string('status')->nullable();
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->string('buildingName')->nullable();
            $table->string('area')->nullable();
            $table->string('landmark');
            $table->string('flatVilla')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('number')->nullable();
            $table->string('whatsapp');
            $table->unsignedBigInteger('service_staff_id')->nullable();
            $table->string('staff_name')->nullable();
            $table->string('date');
            $table->unsignedBigInteger('time_slot_id')->nullable();
            $table->string('time_slot_value')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('order_comment')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');

                $table->foreign('affiliate_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');

                $table->foreign('service_staff_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');

                $table->foreign('time_slot_id')
                ->references('id')
                ->on('time_slots')
                ->onDelete('SET NULL');
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
