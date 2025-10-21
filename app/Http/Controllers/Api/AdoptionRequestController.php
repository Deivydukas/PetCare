<?php

namespace App\Http\Controllers\Api;
use App\Models\AdoptionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class AdoptionRequestController extends Controller
{
    public function index()
    {
        return AdoptionRequest::with('pet')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'status' => 'in:pending,approved,rejected',
        ]);

        $requestEntry = AdoptionRequest::create($data);
        return response()->json($requestEntry, 201);
    }

    public function show($id)
    {
        $adoption = AdoptionRequest::find($id);

        if (!$adoption) {
            return response()->json([
                'error' => 'Adoption record not found',
                'id' => $id
            ], 404);
        }

        return response()->json([
            'message' => 'Adoption record retrieved successfully',
            'adoption' => $adoption
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Find adoption record
        $adoption = AdoptionRequest::find($id);

        if (!$adoption) {
            return response()->json([
                'error' => 'Adoption not found'
            ], 404);
        }

        // Validate incoming data
        $validated = $request->validate([
            'pet_id' => 'sometimes|exists:pets,id',
            'applicant_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'status' => 'sometimes|in:pending,approved,rejected',
        ]);

        try {
            // Update adoption record
            $adoption->update($validated);

            return response()->json([
                'message' => 'Adoption updated successfully',
                'data' => $adoption
            ]);
        } catch (\Exception $e) {
            // Catch unexpected database or validation issues
            return response()->json([
                'error' => 'Failed to update adoption',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
 try {
        // Try to find the adoption request by ID
        $adoptionRequest = AdoptionRequest::find($id);

        if (!$adoptionRequest) {
            return response()->json([
                'error' => 'Adoption request not found',
                'id' => $id
            ], 404);
        }

        // Delete the record
        $adoptionRequest->delete();

        return response()->json([
            'message' => 'Adoption request deleted successfully',
            'deleted_id' => $id
        ], 200);
    } catch (\Exception $e) {
        // Catch unexpected DB or logic errors
        return response()->json([
            'error' => 'Failed to delete adoption request',
            'details' => $e->getMessage()
        ], 500);
    }
    }
}
