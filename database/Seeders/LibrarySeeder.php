<?php

namespace Database\Seeders;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('libraries')->insert([
            'name' => 'Varjohovi',
            'slug' => 'varjohovi',
        ]);
    }
}
