<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'username' => 'amirgjz',
            'email' => 'amir.futurefight@gmail.com',
            'password' => Hash::make('Ag09909165872'),
            'firstname' => 'AmirMohammad',
            'lastname' => 'Ganjizade',
        ]);
    }
}
