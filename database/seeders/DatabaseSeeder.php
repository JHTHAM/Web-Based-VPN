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
            'company_name' => 'NovaFlow Sdn Bhd',
            'company_address' => '30, Jalan Serendah 26/41, Seksyen 26, 40400 Shah Alam, Selangor, Malaysia.',
        ]);

        User::create([
            'username' => 'admin',
            'email' => 'admin@utar.com.my',
            'password' => Hash::make('2222'),
            'company_name' => 'UTAR',
            'company_address' => 'Jalan Sungai Long, Bandar Sungai Long, 43000 Kajang, Selangor',
        ]);
    }
}
