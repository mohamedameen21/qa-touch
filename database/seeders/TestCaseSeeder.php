<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\TestCase;

class TestCaseSeeder extends Seeder
{
    public function run(): void
    {
        // Retrieve all modules from the database
        $modules = Module::all();

        // For each module, create 15 test cases
        $modules->each(function ($module) {
            TestCase::factory()->count(rand(4, 12))->create([
                'module_id' => $module->id,
                'user_id' => $module->user_id, // Assuming each module has a user, or replace with your logic
            ]);
        });
    }
}

