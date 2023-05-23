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
            $table->string('price');
            $table->renameColumn('time', 'time_slot_id');
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
            $table->dropColumn('price');
            $table->renameColumn('time_slot_id', 'time');
        });
    }
};
