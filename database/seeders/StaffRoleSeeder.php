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
        // Create a "Staff" role
        $role = Role::create(['name' => 'Staff']);

        // Define the permissions that "Staff" role should have
        $permissions = Permission::where('name', 'like', 'service-staff%')->pluck('id', 'id');

        // Assign the permissions to the "Staff" role
        $role->syncPermissions($permissions);

        // Create and assign four staff members
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

            // Assign the "Staff" role to the staff member
            $user->assignRole([$role->id]);
        }
    }
}
