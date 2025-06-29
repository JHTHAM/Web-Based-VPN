<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'superadmin',
            'email' => 'superadmin@novaflow.com.my',
            'password' => Hash::make('1111'), // hashed
            'role' => 'superadmin',
        ]);

        User::create([
            'username' => 'admin1',
            'email' => 'admin1@utar.com.my',
            'password' => Hash::make('2222'), // hashed
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'admin2',
            'email' => 'admin2@novaflow.com.my',
            'password' => Hash::make('3333'), // hashed
            'role' => 'admin',
        ]);
    }
}
