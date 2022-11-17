<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sciences = [
            [
                'name' => 'Astronomija',
            ],
            [
                'name' => 'Biologija',
            ],
            [
                'name' => 'Kemija',
            ],
            [
                'name' => 'Računarstvo',
            ],
            [
                'name' => 'Zemljopis',
            ],
            [
                'name' => 'Inženjering',
            ],
            [
                'name' => 'Geografija',
            ],
            [
                'name' => 'Geologija',
            ],
            [
                'name' => 'Matematika',
            ],
            [
                'name' => 'Medicina',
            ],
            [
                'name' => 'Fizika',
            ],
            [
                'name' => 'Psihologija',
            ],
            [
                'name' => 'Sociologija',
            ],
            [
                'name' => 'Filozofija',
            ],
            [
                'name' => 'Ekonomija',
            ],
            [
                'name' => 'Istorija',
            ],
            [
                'name' => 'Pravo',
            ],
            [
                'name' => 'Politologija',
            ],
            [
                'name' => 'Filologija',
            ],
            [
                'name' => 'Lingvistika',
            ],
            [
                'name' => 'Teologija',
            ],
            [
                'name' => 'Teorija književnosti',
            ],
            [
                'name' => 'Kulturologija',
            ],
            [
                'name' => 'Kultura',
            ],
            [
                'name' => 'Umjetnost',
            ],
            [
                'name' => 'Muzika',
            ],
            [
                'name' => 'Dramatika',
            ],
            [
                'name' => 'Likovna umjetnost',
            ],
            [
                'name' => 'Arhitektura',
            ],
        ];
        foreach ($sciences as $science) {
            \App\Models\Science::create($science);
        }
    }
}
