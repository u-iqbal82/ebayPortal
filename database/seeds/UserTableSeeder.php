<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
                    [
                        'name' => 'iThinkMedia',
                        'email' => 'admin@ithinkmedia.co.uk',
                        'password' => bcrypt('iThinkM3dia'),
                        'role' => 'super-admin'
                    ],
                    [
                        'name' => 'Admin',
                        'email' => 'admin1@ithinkmedia.co.uk',
                        'password' => bcrypt('iThinkM3dia'),
                        'role' => 'admin'
                    ],
                    [
                        'name' => 'Feeelancer1',
                        'email' => 'freelancer1@ithinkmedia.co.uk',
                        'password' => bcrypt('iThinkM3dia'),
                        'role' => 'freelancer'
                    ],
                    [
                        'name' => 'Feeelancer2',
                        'email' => 'freelancer2@ithinkmedia.co.uk',
                        'password' => bcrypt('iThinkM3dia'),
                        'role' => 'freelancer'
                    ]
            ];
            
        foreach($users as $user)
        {
            $roleToAssign = $user['role'];
            unset($user['role']);
        
            
            $newUser = User::create($user);
            $newUser->attachRole(Role::where('name', $roleToAssign)->first());
        }
    }
}
