<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // file for each book
        $faker = \Faker\Factory::create();
        $books = \App\Models\Book::all();
        $name = $faker->name;
        foreach ($books as $book) {
            $number = $faker->numberBetween(1, 15);
            $file = \App\Models\ResourceFile::create([
                'uuid' => $number,
                'book_id' => $book->id,
                'folder' => 'images',
                'name' => $number.'.png',
                'filepath' => $number.'.png',
            ]);
        }
    }
}
