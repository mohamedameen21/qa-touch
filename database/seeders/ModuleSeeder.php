<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {

        $userRoot = \App\Models\Module::where('name', 'mohamedameen0786@gmail.com')->first();

        $modules = Module::factory()
            ->count(10)
            ->create();


        foreach ($modules as $module) {
            $module->appendToNode($userRoot)->save();
            $this->createModules($module, 10);
        }
    }

    private function createModules($parent, $depth = 0)
    {
        if ($depth <= 0) {
            return;
        }

        $modules = Module::factory()
            ->count(2)
            ->create();

        foreach ($modules as $module) {
            $module->appendToNode($parent)->save();
            $this->createModules($module, $depth - 1);
        }
    }
}
