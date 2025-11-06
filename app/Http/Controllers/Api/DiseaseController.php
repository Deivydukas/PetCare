<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class DiseaseController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $diseases = Disease::with('healthRecord')->get();
            return response()->json($diseases, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'health_record_id' => 'required|exists:health_records,id',
                'name' => 'required|string|max:255',
                'treatment' => 'nullable|string',
            ]);

            $disease = Disease::create($data);
            return response()->json($disease, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show(Disease $disease): JsonResponse
    {
        try {
            return response()->json($disease->load('healthRecord'), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Disease not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(Request $request, Disease $disease): JsonResponse
    {
        try {
            $data = $request->validate([
                'health_record_id' => 'sometimes|exists:health_records,id',
                'name' => 'sometimes|string|max:255',
                'treatment' => 'nullable|string',
            ]);

            $disease->update($data);
            return response()->json($disease, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy(Disease $disease): JsonResponse
    {
        try {
            $disease->delete();
            return response()->json(['message' => 'Disease deleted'], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    private function errorResponse(\Exception $e): JsonResponse
    {
        return response()->json([
            'error' => 'Server error',
            'message' => $e->getMessage()
        ], 500);
    }
}
