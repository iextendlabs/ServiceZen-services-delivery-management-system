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
        $user = User::create([
            'name' => 'Staff', 
            'email' => 'staff@gmail.com',
            'password' => bcrypt('test')
        ]);

        $user = User::create([
            'name' => 'Staff1', 
            'email' => 'staff1@gmail.com',
            'password' => bcrypt('test')
        ]);

        $user = User::create([
            'name' => 'Staff2', 
            'email' => 'staff2@gmail.com',
            'password' => bcrypt('test')
        ]);

        $user = User::create([
            'name' => 'Staff3', 
            'email' => 'staff3@gmail.com',
            'password' => bcrypt('test')
        ]);

        $staff = Staff::create([
            'user_id' => $user->id, 
            'commission' => '10',
            'phone' => ' '
        ]);
        
        $role = Role::create(['name' => 'Staff']);

        $permissions = Permission::where('name', 'like', 'service-staff%')->pluck('id','id');

        $role->syncPermissions($permissions);
        
        $user->assignRole([$role->id]);
    }
}
