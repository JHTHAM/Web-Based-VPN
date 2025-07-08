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
            'password' => Hash::make('1111'),
        ]);

        User::create([
            'username' => 'admin',
            'email' => 'admin@utar.com.my',
            'password' => Hash::make('2222'),
        ]);

        User::create([
            'username' => 'admin1',
            'email' => 'admin1@novaflow.com.my',
            'password' => Hash::make('3333'),
        ]);
    }
}
