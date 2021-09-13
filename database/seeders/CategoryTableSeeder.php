<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Category;

use Faker\Factory;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Category::truncate();

        // $faker = Faker\Factory::create();
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i ++) {
            Category::create([
                'name' => $faker->name,
            ]);
        }
    }
}
