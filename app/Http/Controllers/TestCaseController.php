<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestCaseController extends Controller
{
    public function index(Request $request, $moduleId)
    {
        try {
            $module = \App\Models\Module::with([
                'test_cases' => function ($query) {
                    $query->select('id', 'module_id', 'ticket_no', 'title', 'file_path');
                }
            ])->select(['id', 'name'])->findOrFail($moduleId);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'module' => $module,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::info('An error occurred while fetching test cases', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching test cases',
            ], 500);
        }
    }
}
