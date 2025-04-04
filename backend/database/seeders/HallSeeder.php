<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('halls')->insert([
            ['name' => 'Halle A'],
            ['name' => 'Halle B'],
            ['name' => 'Halle C'],
        ]);

    }
}
