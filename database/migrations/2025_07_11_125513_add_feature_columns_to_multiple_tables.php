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
        // Add feature column to staff table
        Schema::table('staff', function (Blueprint $table) {
            $table->boolean('feature')->default(0);
        });

        // Add feature column to services table
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('feature')->default(0);
        });

        // Add feature and feature_on_bottom columns to service_categories table
        Schema::table('service_categories', function (Blueprint $table) {
            $table->boolean('feature')->default(0);
            $table->boolean('feature_on_bottom')->default(0);
            $table->integer('sort')->default(0);
        });

        // Add feature column to reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('feature')->default(0);
        });

        // Add feature column to faqs table
        Schema::table('faqs', function (Blueprint $table) {
            $table->boolean('feature')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove columns from staff table
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('feature');
        });

        // Remove columns from services table
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('feature');
        });

        // Remove columns from service_categories table
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropColumn(['feature', 'feature_on_bottom','sort']);
        });

        // Remove columns from reviews table
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('feature');
        });

        // Remove columns from faqs table
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn('feature');
        });
    }
};
