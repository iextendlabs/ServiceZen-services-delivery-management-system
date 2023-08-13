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
        Schema::table('supervisor_to_managers', function (Blueprint $table) {
            $table->foreign('supervisor_id')
                ->references('id')
                ->on('users') // Assuming 'users' is the table name for supervisors
                ->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::table('supervisor_to_managers', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
        });
    }
};
