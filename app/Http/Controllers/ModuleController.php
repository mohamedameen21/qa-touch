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
        $userRoot = Module::where('name', $user->email)->first();
        $modules = $userRoot->descendantsOf($userRoot->id)->toTree($userRoot->id);

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
        $modules = $userRoot->descendantsOf($userRoot->id)->toTree($userRoot->id);

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
