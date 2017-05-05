<?php

use Illuminate\Database\Seeder;
use \App\Role;
use \App\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
                [
                    'name' => 'super-admin',
                    'display_name' => 'Super Admin',
                    'description' => 'Super Admin Role'
                ],
                [
                    'name' => 'admin',
                    'display_name' => 'Admin',
                    'description' => 'Admin Role'
                ],
                [
                    'name' => 'freelancer',
                    'display_name' => 'Freelancer',
                    'description' => 'Freelancer Role'
                ],
            ];
            
        foreach($roles as $key => $value)
        {
            $role = Role::create($value);
            
            if ($value['name'] == 'super-admin')
            {
                $permissions = Permission::all();
                
                foreach($permissions as $p)
                {
                    $role->attachPermission($p);
                }
            }
        }
    }
}
