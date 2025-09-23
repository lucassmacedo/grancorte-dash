<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        $admin = Role::where(['name' => 'admin'])->first();

        $demoUser = User::updateOrCreate(
            [
                'email' => 'luuckymacedo@gmail.com',
            ],
            [
                'nome'              => "Lucas Macedo",
                'password'          => Hash::make('%%2V^8bUK&E8&w6TE#hB2xe5K^2&b5j$X'),
                'is_admin'          => TRUE,
                'email_verified_at' => now(),
            ]
        );

        $demoUser2 = User::updateOrCreate(
            [
                'email' => 'informatica@grancorte.com.br',
            ],
            [
                'nome'              => "Paulo Scalada",
                'is_admin'          => TRUE,
                'password'          => Hash::make('%%2V^8bUK&E8&w6TE#hB2xe5K^2&b5j$X'),
                'email_verified_at' => now(),
            ]);

        $demoUser->assignRole($admin);
        $demoUser2->assignRole($admin);
    }
}
