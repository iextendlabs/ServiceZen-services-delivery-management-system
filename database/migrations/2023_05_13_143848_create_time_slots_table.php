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
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('time_start');
            $table->string('time_end');
            $table->string('date')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();

            $table->foreign('group_id')
                ->references('id')
                ->on('staff_groups')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_slots');
    }
};
