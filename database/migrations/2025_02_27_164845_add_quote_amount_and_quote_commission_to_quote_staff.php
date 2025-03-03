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
        Schema::table('quote_staff', function (Blueprint $table) {
            $table->decimal('quote_amount', 10, 2)->nullable();
            $table->decimal('quote_commission', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quote_staff', function (Blueprint $table) {
            $table->dropColumn(['quote_amount', 'quote_commission','show_quote_detail']);
        });
    }
};
