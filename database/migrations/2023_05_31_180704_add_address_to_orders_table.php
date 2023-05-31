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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('buildingName')->nullable();
            $table->string('area')->nullable();
            $table->string('flatVilla')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('area');
            $table->dropColumn('flatVilla');
            $table->dropColumn('street');
            $table->dropColumn('city');
            $table->dropColumn('number');
        });
    }
};
