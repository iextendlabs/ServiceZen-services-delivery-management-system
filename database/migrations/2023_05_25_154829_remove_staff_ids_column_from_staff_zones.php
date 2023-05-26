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
        Schema::table('staff_zones', function (Blueprint $table) {
            $table->dropColumn('staff_ids');
            $table->string('transport_charges')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_zones', function (Blueprint $table) {
            $table->string('staff_ids')->nullable();
            $table->dropColumn('transport_charges');
        });
    }
};
