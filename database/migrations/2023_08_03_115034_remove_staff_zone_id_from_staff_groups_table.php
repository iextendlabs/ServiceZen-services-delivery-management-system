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

        Schema::table('staff_groups', function (Blueprint $table) {
            $table->dropForeign(['staff_zone_id']);
        });

        Schema::table('staff_groups', function (Blueprint $table) {
            $table->dropColumn('staff_zone_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_zone_id')->nullable();
        });

        Schema::table('staff_groups', function (Blueprint $table) {
            $table->foreign('staff_zone_id')
                ->references('id')
                ->on('staff_zones')
                ->onDelete('CASCADE');
        });
    }
};
