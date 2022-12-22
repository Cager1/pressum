<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed users
        $faker = \Faker\Factory::create();
        // get role "Korisnik"
        $role = \App\Models\Role::where('name', 'Korisnik')->first();
        for ($i = 0; $i < 10; $i++) {
            \App\Models\User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'name' => $faker->name,
                'email' => $faker->email,
                'uid' => $faker->uuid,
                'role_id' => $role->id,
                'branch' => 'none',
            ]);
        }
    }
}
