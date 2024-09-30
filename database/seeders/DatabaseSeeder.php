<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()
            ->count(1)
            ->create([
                'name' => 'Demo User',
                'email' => 'demouser@gmail.com'
            ])
            ->first();

        Module::create([
            'user_id' => 1,
            'name' => $user->email,
        ]);

        $this->call(ModuleSeeder::class);
        $this->call(TestCaseSeeder::class);
    }
}
