<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition(): array
    {
        return [
            'user_id' => 1, // Create associated user
            'name' => ucfirst($this->faker->word),
            'path' => null,
            'position' => null
        ];
    }
}
