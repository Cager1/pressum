<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 100 random authors
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 100; $i++) {
            // get some user
            $user = \App\Models\User::inRandomOrder()->first();
            \App\Models\Author::create([
                'name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'orcid' => $faker->numberBetween(1000000000000, 9999999999999),
                'email' => $faker->email,
                'created_by' => $user->uid,
            ]);
        }
    }
}
