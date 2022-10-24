<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
//        $users = factory(App\User::class, 3)->make();
        // \App\Models\User::factory(10)->create();



        $usersData = [
            [
                'password' => Str::random(10),
                'email' => 'giacomo.terreni@gmail.com',
                'name' => 'Giacomo Terreni',
                'role' => 'Superutente',
            ],
            [
                'password' => Str::random(10),
                'email' => 'ciullo@gmail.com',
                'name' => 'Piero Paolo Ciullo',
                'role' => 'Superutente',
            ],
            [
                'password' => 'amministratore',
                'email' => 'amministratore@amministratore.it',
                'name' => 'Amministratore',
                'role' => 'Admin',
            ],
        ];

        foreach ($usersData as $userData) {
            $user = new User;

            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->remember_token = \Illuminate\Support\Str::random(10);
            //$user->verified = 1;

            $user->save();
            $user->assignRole($userData['role']);


        }



        \Illuminate\Support\Facades\Auth::loginUsingId(3);
        \App\Models\User::factory(10)->create()->each(function($u) {

            //$role = $localizedFaker->boolean(85) ? 'Operatore' : 'Admin'; //15% Admin, 85% Operatore
            $role = rand(0,100) > 80 ? 'Operatore' : 'Cliente';
            $u->assignRole($role);

        });

    }
}
