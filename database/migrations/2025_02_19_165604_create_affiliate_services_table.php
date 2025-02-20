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
        Schema::create('affiliate_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_category_id');
            $table->unsignedBigInteger('service_id');
            $table->enum('commission_type', ['fixed', 'percentage']);
            $table->decimal('commission', 10, 2);
            $table->timestamps();

            $table->foreign('affiliate_category_id')
                ->references('id')
                ->on('affiliate_categories')
                ->onDelete('cascade');

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliate_services');
    }
};
