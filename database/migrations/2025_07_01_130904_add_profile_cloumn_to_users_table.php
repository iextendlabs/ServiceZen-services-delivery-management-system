<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender')->nullable()->after('email');
            $table->string('number')->nullable()->after('gender');
            $table->string('whatsapp')->nullable()->after('number');
        });
        
        DB::table('users')->whereIn('id', function($query) {
        $query->select('user_id')
              ->from('customer_profiles');
        })->update([
            'gender' => DB::raw('(SELECT gender FROM customer_profiles WHERE customer_profiles.user_id = users.id ORDER BY created_at DESC LIMIT 1)'),
            'number' => DB::raw('(SELECT number FROM customer_profiles WHERE customer_profiles.user_id = users.id ORDER BY created_at DESC LIMIT 1)'),
            'whatsapp' => DB::raw('(SELECT whatsapp FROM customer_profiles WHERE customer_profiles.user_id = users.id ORDER BY created_at DESC LIMIT 1)'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'number', 'whatsapp']);
        });
    }
};
