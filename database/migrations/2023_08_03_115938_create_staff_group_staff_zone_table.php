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
        Schema::create('staff_group_staff_zone', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_group_id');
            $table->unsignedBigInteger('staff_zone_id');
            $table->timestamps();

            $table->foreign('staff_group_id')
                ->references('id')
                ->on('staff_groups')
                ->onDelete('cascade');
            $table->foreign('staff_zone_id')
                ->references('id')
                ->on('staff_zones')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_group_staff_zone');
    }
};
