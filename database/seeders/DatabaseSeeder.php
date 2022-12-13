<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionRoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ScienceSeeder::class);
        $this->call(AuthorSeeder::class);
        $this->call(AuthorBookSeeder::class);
        $this->call(BookSeeder::class);
        $this->call(BookScienceSeeder::class);
        $this->call(FileSeeder::class);

    }
}
