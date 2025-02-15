<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('groups')->insert([
            [
                'id' => 1,
                'name' => 'Group 1',
                'category_id' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Group 2',
                'category_id' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Group 3',
                'category_id' => 2,
            ],
        ]);
    }
}
