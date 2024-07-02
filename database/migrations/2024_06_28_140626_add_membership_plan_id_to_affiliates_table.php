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
        Schema::table('affiliates', function (Blueprint $table) {
            $table->unsignedBigInteger('membership_plan_id')->nullable()->after('id');

            $table->foreign('membership_plan_id')
                  ->references('id')
                  ->on('membership_plans')
                  ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropForeign(['membership_plan_id']);
            $table->dropColumn('membership_plan_id');
        });
    }
};
