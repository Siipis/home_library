<?php

namespace Database\Seeders;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment() === 'production') return;

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'no-reply@varjohovi.net',
            'email_verified_at' => \Carbon\Carbon::now(),
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
    }
}
