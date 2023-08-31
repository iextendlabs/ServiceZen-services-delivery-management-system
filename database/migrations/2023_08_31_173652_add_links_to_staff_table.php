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
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('snapchat')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();

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
            $table->dropColumn('instagram');
            $table->dropColumn('facebook');
            $table->dropColumn('snapchat');
            $table->dropColumn('youtube');
            $table->dropColumn('tiktok');

        });
    }
};
