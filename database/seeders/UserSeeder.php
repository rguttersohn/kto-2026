<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Robert',
            'email' => 'rguttersohn@cccnewyork.org',
            'password' => 'testpass',
            'is_admin' => true
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'rguser@gmail.com',
            'password' => 'testpass',
        ]);
    }
}
