<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Shelter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\JsonResponse;
use App\Models\Pet;

class RoomController extends Controller
{
    /**
     * List all rooms for a shelter
     */
    public function index($shelterId): JsonResponse
    {
        try {
            $shelter = Shelter::findOrFail($shelterId);

            $rooms = Room::where('shelter_id', $shelter->id)->get();

            return response()->json(['rooms' => $rooms], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shelter not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }
    public function pets($roomId)
    {
        $pet = Pet::with('photos')->where('room_id', $roomId)->get();
        return response()->json($pet);
    }


    /**
     * Show a single room
     */
    public function show($shelterId, $roomId): JsonResponse
    {
        try {
            $room = Room::findOrFail($roomId);

            if ((int)$room->shelter_id !== (int)$shelterId) {
                return response()->json(['error' => 'Room does not belong to this shelter'], 404);
            }

            return response()->json(['room' => $room], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Create a new room
     */
    public function store(Request $request, $shelterId): JsonResponse
    {
        // $user = JWTAuth::parseToken()->authenticate();
        $user = $request->user();
        try {
            $shelter = Shelter::findOrFail($shelterId);

            if ($resp = $this->checkShelterAccess($user, $shelterId)) {
                return $resp;
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'capacity' => 'required|integer|min:1',
            ]);

            $room = Room::create(array_merge($validated, ['shelter_id' => $shelter->id]));

            return response()->json(['message' => 'Room created successfully', 'room' => $room], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid input data', 'details' => $e->errors()], 422);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid or missing token'], 401);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shelter not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Update a room
     */
    public function update(Request $request, $roomId): JsonResponse
    {
        $user = $request->user();
        try {
            $room = Room::findOrFail($roomId);

            if ($resp = $this->checkShelterAccess($user, $room->shelter_id)) {
                return $resp;
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'capacity' => 'sometimes|required|integer|min:1',
            ]);

            $room->update($validated);

            return response()->json(['message' => 'Room updated successfully', 'room' => $room], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid input data', 'details' => $e->errors()], 422);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid or missing token'], 401);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Delete a room
     */
    public function destroy(Request $request, $roomId): JsonResponse
    {
        $user = $request->user();
        try {
            $room = Room::findOrFail($roomId);

            if ($resp = $this->checkShelterAccess($user, $room->shelter_id)) {
                return $resp;
            }

            $room->delete();

            return response()->json(['message' => 'Room deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found'], 404);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid or missing token'], 401);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Centralized shelter access check
     */
    private function checkShelterAccess($user, $shelterId): ?JsonResponse
    {
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($user->role === 'admin') {
            return null;
        }

        if ($user->role === 'worker') {
            if ((int)$user->shelter_id === (int)$shelterId) {
                return null;
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
        return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
    }
}
