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
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);

            $table->dropColumn('supervisor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->foreign('supervisor_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });
    }
};
