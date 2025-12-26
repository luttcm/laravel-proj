<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();
        
        User::create([
            'name' => 'Администратор',
            'email' => 's.shemyatenkov@itgrade.ru',
            'password' => Hash::make('1234567qweR'),
            'role' => 'admin',
        ]);
    }
}
