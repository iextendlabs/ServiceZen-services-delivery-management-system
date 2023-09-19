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
        Schema::table('short_holidays', function (Blueprint $table) {
            $table->integer('start_time_to_sec');
            $table->integer('end_time_to_sec');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('short_holidays', function (Blueprint $table) {
            $table->dropColumn(['start_time_to_sec', 'end_time_to_sec']);
        });
    }
};
