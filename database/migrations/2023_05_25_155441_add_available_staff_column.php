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
        Schema::table('time_slots', function (Blueprint $table) {
            $table->string('available_staff')->nullable()->after('group_id');
            $table->string('name')->after('id');
            $table->string('space_availability')->nullable()->after('group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->dropColumn('available_staff');
            $table->dropColumn('name');
            $table->dropColumn('space_availability');
        });
    }
};
