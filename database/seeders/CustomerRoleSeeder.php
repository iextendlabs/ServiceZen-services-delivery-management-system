<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CustomerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Customer', 
            'email' => 'customer@gmail.com',
            'password' => bcrypt('test')
        ],
        [
            'name' => 'Customer1', 
            'email' => 'customer1@gmail.com',
            'password' => bcrypt('test')
        ],
        [
            'name' => 'Customer2', 
            'email' => 'customer2@gmail.com',
            'password' => bcrypt('test')
        ],
        [
            'name' => 'Customer3', 
            'email' => 'customer3@gmail.com',
            'password' => bcrypt('test')
        ]);
        
        $role = Role::create(['name' => 'Customer']);

        $permissions = Permission::where('name', 'like', 'customer%')->pluck('id','id');

        $role->syncPermissions($permissions);
        
        $user->assignRole([$role->id]);
    }
}
