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
        Schema::table('affiliate_categories', function (Blueprint $table) {
            $table->enum('commission_type', ['fixed', 'percentage'])->default('percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('affiliate_categories', function (Blueprint $table) {
            $table->dropColumn('commission_type');
        });
    }
};
