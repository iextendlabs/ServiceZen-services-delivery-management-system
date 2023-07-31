<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class StaffRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'Staff']);
        
        $permissionNames = [
            'service-staff-list',
            'service-staff-edit',
            'service-staff-create',
            'service-staff-delete',
            'order-list',
            'order-edit',
            'menu-sales'
        ];
        
        $permissions = Permission::whereIn('name', $permissionNames)->pluck('id');

        $role->syncPermissions($permissions);

        for ($i = 1; $i <= 4; $i++) {
            $user = User::create([
                'name' => 'Staff ' . $i,
                'email' => 'staff' . $i . '@gmail.com',
                'password' => bcrypt('test')
            ]);

            Staff::create([
                'user_id' => $user->id,
                'commission' => '10',
                'phone' => ' '
            ]);

            $user->assignRole([$role->id]);
        }
    }
}
