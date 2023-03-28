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
        Schema::table('service_appointments', function (Blueprint $table) {
            $table->date('date');
            $table->time('time');
            $table->string('address');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_appointments', function (Blueprint $table) {
            $table->dropColumn(['date', 'time','address','status']);
        });
    }
};
