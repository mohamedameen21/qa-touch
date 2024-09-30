<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TestCase>
 */
class TestCaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'module_id' => Module::factory(),
            'ticket_no' => strtoupper($this->faker->unique()->bothify('TICKET-#####')),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'file_path' => null,
        ];
    }
}
