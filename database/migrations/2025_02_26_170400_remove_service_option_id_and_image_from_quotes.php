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
            // Drop foreign key before removing the column
            $table->dropForeign(['service_option_id']);
            $table->dropColumn(['service_option_id', 'image']);
            
            // Add new columns
            $table->string('location')->nullable();
            $table->unsignedBigInteger('affiliate_id')->nullable();

            // Add foreign key for affiliate_id
            $table->foreign('affiliate_id')
                ->references('id')
                ->on('users')
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
            // Drop foreign key before removing the column
            $table->dropForeign(['affiliate_id']);
            $table->dropColumn(['affiliate_id', 'location']);

            // Re-add removed columns
            $table->unsignedBigInteger('service_option_id')->nullable()->after('service_name');
            $table->string('image')->nullable()->after('detail');

            // Restore foreign key for service_option_id
            $table->foreign('service_option_id')
                ->references('id')
                ->on('service_options')
                ->onDelete('SET NULL');

        });
    }
};
