<?php

namespace Database\Seeders;

use App\Models\Affiliate;
use App\Models\User;
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
            'password' => bcrypt('test')
        ]);

        $affiliate = Affiliate::create([
            'user_id' => $user->id, 
            'code' => '1111',
            'commission' => '10'
        ]);
        
        $role = Role::create(['name' => 'Affiliate']);

        $permissions = Permission::where('name', 'like', 'affiliate%')->pluck('id','id');

        $role->syncPermissions($permissions);
        
        $user->assignRole([$role->id]);
    }
}
