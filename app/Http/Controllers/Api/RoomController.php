<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Shelter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoomController extends Controller
{
    /**
     * List all rooms for a shelter
     */
    public function index($shelterId): JsonResponse
    {
        try {
            $rooms = Room::where('shelter_id', $shelterId)->get();
            return response()->json(['rooms' => $rooms], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Show a single room
     */
    public function show($shelterId, Room $room): JsonResponse
    {
        try {
            return response()->json(['room' => $room], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Store a new room
     */
    public function store(Request $request, $shelterId): JsonResponse
    {
        try {
            $shelter = Shelter::findOrFail($shelterId);
            $user = JWTAuth::parseToken()->authenticate();

            // Check permissions
            if ($resp = $this->checkShelterAccess($user, $shelterId)) {
                return $resp;
            }

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'capacity' => 'required|integer|min:1',
            ]);

            $room = Room::create(array_merge($data, ['shelter_id' => $shelter->id]));

            return response()->json(['message' => 'Room created successfully', 'room' => $room], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shelter not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid input data', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Update a room
     */
    public function update(Request $request, Room $room): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Check permissions
            if ($resp = $this->checkShelterAccess($user, $room->shelter_id)) {
                return $resp;
            }

            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'capacity' => 'sometimes|required|integer|min:1',
            ]);

            $room->update($data);

            return response()->json(['message' => 'Room updated successfully', 'room' => $room], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid input data', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Delete a room
     */
    public function destroy($id): JsonResponse
    {
        try {
            $room = Room::findOrFail($id);
            $user = JWTAuth::parseToken()->authenticate();

            // Check permissions
            if ($resp = $this->checkShelterAccess($user, $room->shelter_id)) {
                return $resp;
            }

            $room->delete();

            return response()->json(['message' => 'Room deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Centralized shelter access check
     */
    private function checkShelterAccess($user, $shelterId): ?JsonResponse
    {
        $shelterId = (int) $shelterId;

        if ($user->role === 'admin') {
            return null; // Admin can access everything
        }

        if ($user->role === 'worker') {
            if ($user->shelter_id === $shelterId) {
                return null; // Worker can access their shelter
            }
            return response()->json(['error' => 'Forbidden: Not your shelter'], 403);
        }

        return response()->json(['error' => 'Forbidden: Insufficient permissions'], 403);
    }

    /**
     * JSON response for server errors
     */
    private function errorResponse(\Exception $e): JsonResponse
    {
        return response()->json([
            'error' => 'Server error',
            'message' => $e->getMessage()
        ], 500);
    }
}
