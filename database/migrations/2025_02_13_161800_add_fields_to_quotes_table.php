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
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('bid_id')->nullable();
            $table->integer('sourcing_quantity')->nullable();

            $table->foreign('bid_id')
                ->references('id')
                ->on('bids')
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
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['bid_id']);
            $table->dropColumn(['phone', 'whatsapp', 'image', 'sourcing_quantity', 'bid_id']);
        });
    }
};
