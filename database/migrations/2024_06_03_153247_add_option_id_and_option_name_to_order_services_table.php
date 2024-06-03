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
        Schema::table('order_services', function (Blueprint $table) {
            $table->unsignedBigInteger('option_id')->nullable();
            $table->string('option_name')->nullable();

            $table->foreign('option_id')
                ->references('id')
                ->on('service_options')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_services', function (Blueprint $table) {
            $table->dropColumn('option_id');
            $table->dropColumn('option_name');
        });
    }
};
