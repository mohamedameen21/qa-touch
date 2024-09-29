<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewModuleRequest;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ModuleController extends Controller
{
    public function index()
    {
        $userRoot = Module::where('name', 'mohamedameen0786@gmail.com')->first();
        $modules = $userRoot->descendantsOf($userRoot->id)->toTree($userRoot->id);

        $tree = $this->buildTree($modules);

        return Inertia::render('Dashboard', [
            'modules' => $tree
        ]);
    }

    public function getDirectChildren(Request $request, $moduleId = null)
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

    public function store(NewModuleRequest $request, $moduleId = null)
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

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

    }

    private function buildTree($modules): array
    {
        return $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'name' => $module->name,
                'open' => false,
                'children' => $this->buildTree($module->children)
            ];
        })->toArray();
    }
}
