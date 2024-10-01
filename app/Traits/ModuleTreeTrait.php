<?php

namespace App\Traits;

use App\Models\Module;
use Illuminate\Support\Facades\Log;

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

    private function reOrderWithinParent($module, $oldIndex, $newIndex)
    {
        $hasChanged = true;
        $steps = abs($newIndex - $oldIndex);
        if ($newIndex > $oldIndex && $steps > 0) {
            $hasChanged = $module->down($steps);
            Log::info('Down', ['hasChanged' => $hasChanged]);
        } else if ($steps > 0) {
            $hasChanged = $module->up($steps);
            Log::info('Up', ['hasChanged' => $hasChanged]);
        }

        return $hasChanged;
    }

    private function moveToNewParent($module, $newParentId, $newIndex)
    {
        $newParent = Module::withCount('children')->find($newParentId);
        $module->parent_id = $newParent->id;
        if ($module->save() && $module->hasMoved()) {
            $isReOrdered = $this->reOrderWithinParent($module, $newParent->children_count, $newIndex);

            return $isReOrdered;
        }

        return false;
    }
}
























