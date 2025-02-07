<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test Jhon',
            'email' => 'jhon@testexample.com',
        ]);

        User::factory(10)->create();

        $this->call(BoardSeeder::class);
    }
}
