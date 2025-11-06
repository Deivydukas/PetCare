<?php

namespace App\Http\Controllers\Api;

use App\Models\Pet;
use App\Http\Resources\PetResource;
use App\Models\PetPhoto;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class PetController extends Controller
{
    /**
     * List all pets in a room
     */
    public function index($roomId): JsonResponse
    {
        try {
            $pets = Pet::where('room_id', $roomId)->get();
            return response()->json(['pets' => $pets], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Store a new pet
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'species' => 'required|string|max:255',
                'breed' => 'nullable|string|max:255',
                'age' => 'nullable|integer|between:0,25',
                'status' => 'in:available,adopted',
                'room_id' => 'required|exists:rooms,id',
            ]);

            $room = Room::findOrFail($data['room_id']);

            // Check permissions
            if ($resp = $this->checkShelterAccess($user, $room->shelter_id)) {
                return $resp;
            }

            // Check capacity
            $currentCount = Pet::where('room_id', $room->id)->count();
            if ($currentCount >= $room->capacity) {
                return response()->json([
                    'error' => 'This room is already at full capacity.',
                    'current_count' => $currentCount,
                    'capacity' => $room->capacity,
                ], 422);
            }

            $pet = Pet::create($data);

            return response()->json([
                'message' => 'Pet created successfully',
                'pet' => $pet
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid input data', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Show a single pet
     */
    public function show(Pet $pet): JsonResponse
    {
        try {
            return response()->json(new PetResource($pet), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pet not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Update a pet
     */
    public function update(Request $request, Pet $pet): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $room = Room::findOrFail($pet->room_id);

            // Check permissions
            if ($resp = $this->checkShelterAccess($user, $room->shelter_id)) {
                return $resp;
            }

            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'species' => 'sometimes|required|string|max:255',
                'breed' => 'nullable|string|max:255',
                'age' => 'nullable|integer',
                'status' => 'in:available,adopted',
            ]);

            $pet->update($data);

            return response()->json(['message' => 'Pet updated successfully', 'pet' => new PetResource($pet)], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid input data', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Room not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Delete a pet
     */
    public function destroy(Pet $pet): JsonResponse
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $room = Room::findOrFail($pet->room_id);

            // Check permissions
            if ($resp = $this->checkShelterAccess($user, $room->shelter_id)) {
                return $resp;
            }

            $pet->delete();

            return response()->json(['message' => 'Pet deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pet or Room not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Upload pet photo
     */
    public function uploadPhoto(Request $request, $petId): JsonResponse
    {
        try {
            $pet = Pet::findOrFail($petId);
            $user = JWTAuth::parseToken()->authenticate();
            $room = Room::findOrFail($pet->room_id);

            // Check permissions
            if ($resp = $this->checkShelterAccess($user, $room->shelter_id)) {
                return $resp;
            }

            $request->validate(['photo' => 'required|image|max:5120']);

            $path = $request->file('photo')->store('pets', 'public');

            $photo = PetPhoto::create([
                'pet_id' => $pet->id,
                'file_path' => $path
            ]);

            return response()->json(['message' => 'Photo uploaded successfully', 'data' => $photo], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid input data', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pet not found'], 404);
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
