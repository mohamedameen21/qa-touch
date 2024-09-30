<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestCaseRequest;
use App\Models\Module;
use App\Models\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TestCaseController extends Controller
{
    public function index(Request $request, $moduleId)
    {
        try {
            $module = \App\Models\Module::with([
                'test_cases' => function ($query) {
                    $query->select('id', 'module_id', 'ticket_no', 'title', 'description', 'file_path')->orderBy('created_at', 'desc');
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

    public function store(TestCaseRequest $request, $moduleId)
    {
        try {
            $file = $request->file('file');
            $filePath = $file ? $this->storeAndGetFilePath($file) : null;

            TestCase::create([
                'user_id' => auth()->id(),
                'module_id' => $moduleId,
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Test case created successfully',
            ], 201);

        } catch (\Exception $e) {
            Log::info('An error occurred while creating a test case', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating a test case',
            ], 500);
        }
    }

    public function update(TestCaseRequest $request, $moduleId, $testCaseId)
    {
        try {
            $testCase = TestCase::where('module_id', $moduleId)->findOrFail($testCaseId);

            $file = $request->file('file');
            $filePath = $testCase->file_path;

            if($file && $testCase->file_path) {
                Storage::disk('public')->delete($testCase->file_path);
                $filePath = $this->storeAndGetFilePath($file);
            }

            $testCase->update([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $filePath,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Test case updated successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::info('An error occurred while updating a test case', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating a test case',
            ], 500);
        }
    }

    public function destroy($moduleId, $testCaseId)
    {
        try {
            $testCase = TestCase::where('module_id', $moduleId)->findOrFail($testCaseId);

            if($testCase->file_path) {
                Storage::disk('public')->delete($testCase->file_path);
            }

            $testCase->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Test case deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::info('An error occurred while deleting a test case', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting a test case',
            ], 500);
        }
    }

    private function storeAndGetFilePath($file)
    {
        $userId = auth()->id();
        $originalName = $file->getClientOriginalName();
        $fileName = pathinfo($originalName, PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs("uploads/{$userId}/testCases", $fileName, 'public');

        return $filePath;
    }
}
