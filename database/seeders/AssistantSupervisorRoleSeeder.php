<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class AssistantSupervisorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Assistant Supervisor', 
            'email' => 'assistantsupervisor@gmail.com',
            'password' => bcrypt('assistantsupervisor1234')
        ]);
        
        $role = Role::create(['name' => 'Assistant Supervisor']);

        $permissions = Permission::where('name', 'like', 'assistant-supervisor%')->pluck('id','id');

        $role->syncPermissions($permissions);
        
        $user->assignRole([$role->id]);
    }
}
