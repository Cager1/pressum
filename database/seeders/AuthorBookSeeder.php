<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AuthorBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $authors = \App\Models\Author::all();
        $books = \App\Models\Book::all();
        foreach ($books as $book) {
            $number = $faker->numberBetween(1, 4);
            for ($i = 0; $i < $number; $i++) {
                \App\Models\AuthorBook::create([
                    'author_id' => $faker->numberBetween(1, 100),
                    'book_id' => $book->id,
                ]);
            }
        }
    }
}
