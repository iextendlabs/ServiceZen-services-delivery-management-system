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
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->decimal('currency_rate', 10, 2)->nullable();
            $table->string('extra_charges')->nullable();
            $table->foreign('currency_id')
            ->references('id')
            ->on('currencies')
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
            $table->dropColumn('currency_rate');
            $table->dropColumn('extra_charges');
        });
    }
};
