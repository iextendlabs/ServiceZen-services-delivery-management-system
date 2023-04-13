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
        // Add the 'appointment_id' column
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('appointment_id')->nullable()->after('id');
        });

        // Rename the 'payment' column to 'amount'
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('payment', 'amount');
        });
    }

    public function down()
    {
        // Rename the 'amount' column back to 'payment'
        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('amount', 'payment');
        });

        // Remove the 'appointment_id' column
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('appointment_id');
        });
    }
};
