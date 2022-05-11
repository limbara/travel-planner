<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->state([
            'name' => 'Nico',
            'email' => 'Nico@example.com',
        ])->has(Trip::factory())->create();
        
        User::factory()->state([
            'name' => 'Erwin',
            'email' => 'Erwin@example.com',
        ])->has(Trip::factory())->create();
    }
}
