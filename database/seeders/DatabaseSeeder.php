<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // RoleSeeder HARUS dipanggil duluan sebelum UserSeeder
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}