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
        Schema::table('service_categories', function (Blueprint $table) {
            // First drop the existing foreign key
            $table->dropForeign(['parent_id']);

            // Then add the new one with set null on delete
            $table->foreign('parent_id')
                ->references('id')
                ->on('service_categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);

            $table->foreign('parent_id')
                ->references('id')
                ->on('service_categories')
                ->onDelete('cascade');
        });
    }
};
