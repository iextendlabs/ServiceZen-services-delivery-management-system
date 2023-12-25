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
            $table->integer('hours')->after('time_end');
            $table->dropColumn('time_end');
            $table->dropColumn('end_time_to_sec');
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
            $table->time('time_end')->after('hours');
            $table->integer('end_time_to_sec');
            $table->dropColumn('hours');
        });
    }
};
