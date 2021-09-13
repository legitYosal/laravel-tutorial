<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::truncate();

        $faker = Factory::create();

        $same_password = Hash::make('123');

        User::create([
            'name'=> 'admin',
            'email'=> 'flaslnm@gmail.com',
            'password'=> $same_password,
        ]);
        for ($i = 0; $i < 10; $i ++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => $same_password,
            ]);
        }
    }
}
