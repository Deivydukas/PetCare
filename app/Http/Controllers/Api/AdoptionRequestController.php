<?php

namespace App\Http\Controllers\Api;

use App\Models\AdoptionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdoptionRequestController extends Controller
{
    /**
     * List adoption requests
     * - Admin & Worker: all requests
     * - User: only their own requests
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            if (in_array($user->role, ['worker', 'admin'])) {
                $requests = AdoptionRequest::with('pet')->get();
            } else {
                $requests = AdoptionRequest::with('pet')
                    ->where('email', $user->email)
                    ->get();
            }

            return response()->json([
                'message' => 'Adoption requests retrieved successfully',
                'requests' => $requests
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve adoption requests',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show single adoption request
     * - Admin & Worker: can view any request
     * - User: can view only their own request
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $adoption = AdoptionRequest::with('pet')->find($id);

            if (!$adoption) {
                return response()->json(['error' => 'Adoption request not found'], 404);
            }

            if (!$user->hasRole(['worker', 'admin']) && $adoption->email !== $user->email) {
                return response()->json(['error' => 'Forbidden: Not your adoption request'], 403);
            }

            return response()->json([
                'message' => 'Adoption request retrieved successfully',
                'adoption' => $adoption
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve adoption request',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new adoption request
     * - Any authenticated user
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'applicant_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'status' => 'sometimes|in:pending,approved,rejected'
            ]);

            $adoption = AdoptionRequest::create($validated);

            return response()->json([
                'message' => 'Adoption request created successfully',
                'adoption' => $adoption
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create adoption request',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update adoption request
     * - Only the user who created it or admin/worker
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        try {
            $adoption = AdoptionRequest::findOrFail($id);

            // Only admin/worker or the owner can update
            if (!in_array($user->role, ['worker', 'admin']) && $adoption->email !== $user->email) {
                return response()->json(['error' => 'Forbidden: Cannot update this adoption request'], 403);
            }

            $validated = $request->validate([
                'pet_id' => 'sometimes|exists:pets,id',
                'applicant_name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255',
                'status' => 'sometimes|in:pending,approved,rejected'
            ]);

            $adoption->update($validated);

            return response()->json([
                'message' => 'Adoption request updated successfully',
                'adoption' => $adoption
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Adoption request not found'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update adoption request',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Delete adoption request
     * - Only the user who created it or admin/worker
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $adoption = AdoptionRequest::find($id);

            if (!$adoption) {
                return response()->json(['error' => 'Adoption request not found'], 404);
            }

            // Only admin/worker or the owner can delete
            if (!in_array($user->role, ['worker', 'admin']) && $adoption->email !== $user->email) {
                return response()->json(['error' => 'Forbidden: Cannot delete this adoption request'], 403);
            }

            $adoption->delete();

            return response()->json([
                'message' => 'Adoption request deleted successfully',
                'deleted_id' => $id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete adoption request',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
