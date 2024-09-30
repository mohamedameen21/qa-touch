<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {

        $user = User::first();
        $userRoot = Module::where('name', $user->email)->first();

        $modules = Module::factory()
            ->count(rand(2, 4))
            ->create();

        foreach ($modules as $index => $module) {
            $module->order = $index;
            $module->appendToNode($userRoot)->save();
            $this->createModules($module, rand(3, 6));
        }
    }

    private function createModules($parent, $depth = 0)
    {
        if ($depth <= 0) {
            return;
        }

        $modules = Module::factory()
            ->count(rand(1, 4))
            ->create();

        foreach ($modules as $index => $module) {
            $module->order = $index;
            $module->appendToNode($parent)->save();
            $this->createModules($module, $depth - 1);
        }
    }
}
