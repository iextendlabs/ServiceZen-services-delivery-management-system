<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class SupervisorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Supervisor', 
            'email' => 'supervisor@gmail.com',
            'password' => bcrypt('supervisor1234')
        ]);
        
        $role = Role::create(['name' => 'Supervisor']);

        $permissions = Permission::where('name', 'like', 'supervisor%')->pluck('id','id');

        $role->syncPermissions($permissions);
        
        $user->assignRole([$role->id]);
    }
}
