<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('categories')->insert([
            [
                'id'         => 1,
                'name'       => 'Gemischt',
                'color'      => '#fb7474',
                'subgroups'  => 2,
            ],
            [
                'id'         => 2,
                'name'       => 'Leitende',
                'color'      => '#73e873',
                'subgroups'  => 1,
            ],
        ]);
    }
}