<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class ManagerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Manager', 
            'email' => 'manager@gmail.com',
            'password' => bcrypt('test')
        ]);

        $role = Role::create(['name' => 'Manager']);

        $permissionNames = [
            'manager-list',
            'manager-edit',
            'manager-create',
            'manager-delete',
            'order-list',
            'order-edit',
            'order-download',
            'menu-sales'
        ];
        
        $permissions = Permission::whereIn('name', $permissionNames)->pluck('id');

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
