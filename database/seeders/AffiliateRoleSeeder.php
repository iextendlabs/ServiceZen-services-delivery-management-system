<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class AffiliateRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Affiliate', 
            'email' => 'affiliate@gmail.com',
            'password' => bcrypt('affiliate1234')
        ]);
        
        $role = Role::create(['name' => 'Affiliate']);

        $permissions = Permission::where('name', 'like', 'affiliate%')->pluck('id','id');

        $role->syncPermissions($permissions);
        
        $user->assignRole([$role->id]);
    }
}
