<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('teams')->insert([
            [
                'id' => 1,
                'name' => 'Pippi',
                'section_id' => 6,
                'category_id' => 1,
            ],
            [
                'id' => 2,
                'name' => 'no wilder(t)',
                'section_id' => 5,
                'category_id' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Gryfensee',
                'section_id' => 1,
                'category_id' => 1,
            ],
            [
                'id' => 4,
                'name' => 'Piranhaha',
                'section_id' => 4,
                'category_id' => 1,
            ],
            [
                'id' => 5,
                'name' => 'Hai elei',
                'section_id' => 4,
                'category_id' => 1,
            ],
            [
                'id' => 6,
                'name' => 'zu wild(ert)',
                'section_id' => 5,
                'category_id' => 1,
            ],
            [
                'id' => 7,
                'name' => 'Wikim',
                'section_id' => 2,
                'category_id' => 1,
            ],
            [
                'id' => 8,
                'name' => 'Radiazun',
                'section_id' => 3,
                'category_id' => 1,

            ],
            [
                'id' => 9,
                'name' => 'Running Gag',
                'section_id' => 4,
                'category_id' => 2,
            ],
            [
                'id' => 10,
                'name' => 'Glunk',
                'section_id' => 5,
                'category_id' => 2,
            ],
            [
                'id' => 11,
                'name' => 'Langstrumpf',
                'section_id' => 6,
                'category_id' => 2,
            ],
            [
                'id' => 12,
                'name' => 'Not for fun',
                'section_id' => 4,
                'category_id' => 2,
            ],
            [
                'id' => 13,
                'name' => 'die Strahlende',
                'section_id' => 3,
                'category_id' => 2,
            ],
        ]);
    }
}
