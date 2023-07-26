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
            'password' => bcrypt('test')
        ]);
        
        $role = Role::create(['name' => 'Supervisor']);

        $permissionNames = [
            'supervisor-list',
            'supervisor-edit',
            'supervisor-create',
            'supervisor-delete',
            'order-list',
            'order-edit',
            'order-download',
            'order-delete',
            'menu-sales'
        ];
        
        $permissions = Permission::whereIn('name', $permissionNames)->pluck('id');

        $role->syncPermissions($permissions);
        
        $user->assignRole([$role->id]);
    }
}
