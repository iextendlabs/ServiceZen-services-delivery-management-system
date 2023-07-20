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
        Schema::create('cash_collections', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('amount');
            $table->string('staff_name');
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('status');
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('CASCADE');

                $table->foreign('staff_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('cash_collections');
    }
};
