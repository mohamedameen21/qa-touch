<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleCreateUpdateRequest;
use App\Models\Module;
use App\Traits\ModuleTreeTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ModuleController extends Controller
{
    use ModuleTreeTrait;

    public function index(Request $request)
    {
        $user = auth()->user();
        $userRoot = Module::defaultOrder()->where('name', $user->email)->first();
        $modules = Module::defaultOrder()->descendantsOf($userRoot->id)->toTree($userRoot->id);

        $tree = $this->buildTree($modules);

        return Inertia::render('Dashboard', [
            'modules' => $tree,
            'rootId' => $userRoot->id
        ]);
    }

    public function refresh(Request $request)
    {
        $modules = $request->input('modules');
        $openModulesIds = $this->getOpenModules($modules);

        $user = auth()->user();
        $userRoot = Module::where('name', $user->email)->first();
        $modules = Module::defaultOrder()->descendantsOf($userRoot->id)->toTree($userRoot->id);

        $tree = $this->buildTree($modules, $openModulesIds);

        return response()->json([
            'status' => 'success',
            'data' => ['modules' => $tree]
        ]);
    }

    public function getSubModules(Request $request, $moduleId = null)
    {
        $user = auth()->user();
        $userRoot = Module::where('name', $user->email)->first();
        $module = $moduleId ? Module::find($moduleId) : $userRoot;

        $directModules = $module->children->map(function ($module) {
            return [
                'id' => $module->id,
                'name' => $module->name,
            ];
        })->toArray();

        return response()->json([
            'status' => 'success',
            'data' => ['modules' => $directModules]
        ]);
    }

    public function store(ModuleCreateUpdateRequest $request)
    {
        DB::beginTransaction();

        try {
            $parent = $request->parent;

            $newModule = Module::create([
                'name' => $request->name,
                'parent_id' => $parent->id,
                'user_id' => auth()->id()
            ]);

            $parent->appendNode($newModule);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Module created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Failed to create module', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

    }

    public function syncDrag(Request $request)
    {
        $moduleId = $request->input('id');
        $oldParentId = $request->input('oldParentId');
        $newParentId = $request->input('newParentId');
        $oldIndex = $request->input('oldIndex');
        $newIndex = $request->input('newIndex');

        DB::beginTransaction();
        try {
            $module = Module::findOrFail($moduleId);

            $module->refreshNode();

            if ($oldParentId === $newParentId) {
                $hasChanged = true;
                $steps = abs($newIndex - $oldIndex);
                if ($newIndex > $oldIndex && $steps > 0) {
                    $hasChanged = $module->down($steps);
                    Log::info('Down', ['hasChanged' => $hasChanged]);
                } else if ($steps > 0) {
                    $hasChanged = $module->up($steps);
                    Log::info('Up', ['hasChanged' => $hasChanged]);
                }

                if ($hasChanged) {
                    DB::commit();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Module moved successfully'
                    ]);
                } else {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to move module'
                    ], 500);
                }

            } else {
                $newParent = Module::find($newParentId);
                $module->parent_id = $newParent->id;
                if ($module->save()) {
                    if ($module->hasMoved()) {
                        DB::commit();

                        return response()->json([
                            'status' => 'success',
                            'message' => 'Module moved successfully'
                        ]);
                    } else {
                        DB::rollBack();

                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to move module'
                        ], 500);
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Failed to move module', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to move module'
            ], 500);
        }
    }

    public function update(ModuleCreateUpdateRequest $request, $moduleId)
    {
        try {
            $module = Module::findorFail($moduleId);

            $module->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Module updated successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::info('Failed to update module', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update module'
            ], 500);
        }
    }

    public function destroy(Request $request, $moduleId)
    {
        DB::beginTransaction();
        try {
            $module = Module::findOrFail($moduleId);
            $module->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Module deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Failed to delete module', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete module'
            ], 500);
        }
    }
}
