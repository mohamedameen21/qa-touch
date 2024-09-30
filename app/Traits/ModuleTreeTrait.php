<?php

namespace App\Traits;

trait ModuleTreeTrait
{
    private function buildTree($modules, $openedModules = null): array
    {
        return $modules->map(function ($module) use ($openedModules) {
            return [
                'id' => $module->id,
                'name' => $module->name,
                'order' => $module->order,
                'open' => $openedModules ? in_array($module->id, $openedModules) : false,
                'children' => $this->buildTree($module->children, $openedModules)
            ];
        })->toArray();
    }

    private function getOpenModules($modules)
    {
        $openModules = [];
        foreach ($modules as $module) {
            if ($module['open']) {
                $openModules[] = $module['id'];
            }
            if (isset($module['children'])) {
                $openModules = array_merge($openModules, $this->getOpenModules($module['children']));
            }
        }

        return $openModules;
    }
}
