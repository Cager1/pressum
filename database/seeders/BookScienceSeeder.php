<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BookScienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $books = \App\Models\Book::all();
        $sciences = \App\Models\Science::all();
        foreach ($books as $book) {
            $number = $faker->numberBetween(1, 4);
            for ($i = 0; $i < $number; $i++) {
                $n = $faker->numberBetween(0, $sciences->count() - 1);
                \App\Models\BookScience::create([
                    'book_id' => $book->id,
                    'science_id' => $sciences[$n]->id,
                ]);
            }
        }
    }
}
