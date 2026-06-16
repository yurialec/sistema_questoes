<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Yuri',
            'email' => 'yuri.alec@hotmail.com',
            'password' => bcrypt('Ya_913526124500')
        ]);
    }
}
