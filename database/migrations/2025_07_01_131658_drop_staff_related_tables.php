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
        Schema::dropIfExists('staff_group_to_staff');
        Schema::dropIfExists('staff_group_staff_zone');
        Schema::dropIfExists('staff_group_driver');
        Schema::dropIfExists('staff_groups');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // throw new RuntimeException("This migration cannot be rolled back automatically. The original table structures are required to properly implement the down() method.");
    }
};
