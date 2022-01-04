<?php

namespace Database\Seeders;


use App\Book;
use App\Library;
use Illuminate\Support\Facades\App;
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

        if (App::environment() === 'local') {
            factory(Book::class, 10)->create([
                'library_id' => 1
            ]);
        }
    }
}
