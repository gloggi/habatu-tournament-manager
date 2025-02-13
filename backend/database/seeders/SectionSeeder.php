<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sections')->insert([
            [
                'id'   => 1,
                'name' => 'Gryfenssee',  
            ],
            [
                'id'   => 2,
                'name' => 'Hadlaub',     
            ],
            [
                'id'   => 3,
                'name' => 'Lägern',      
            ],
            [
                'id'   => 4,
                'name' => 'See',         
            ],
            [
                'id'   => 5,
                'name' => 'Wildert',     
            ],
            [
                'id'   => 6,
                'name' => 'Manegg',      
            ],
        ]);
    }
}
