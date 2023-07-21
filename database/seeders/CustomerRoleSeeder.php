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
        $permissions = Permission::where('name', 'like', 'customer%')->pluck('id','id');

        $role = Role::create(['name' => 'Customer']);
        $role->syncPermissions($permissions);

        for ($i = 0; $i <= 4; $i++) {
            $user = User::create([
                'name' => "Customer {$i}", 
                'email' => "customer{$i}@gmail.com",
                'password' => bcrypt('test')
            ]);
        
            $user->assignRole([$role->id]);
        }
    }
}
