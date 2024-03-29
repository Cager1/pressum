<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed books
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 100; $i++) {
            // get some user
            $user = \App\Models\User::inRandomOrder()->first();
            \App\Models\Book::create([
                'name' => $faker->text(25),
                'isbn' => $faker->numberBetween(1000000000000, 9999999999999),
                'created_by' => $user->uid,
                'slug' => $faker->slug,
            ]);
        }
    }
}
