<?php

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
        if (Env::production()) return;

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'no-reply@varjohovi.net',
            'password' => Hash::make('password'),
        ]);
    }
}
