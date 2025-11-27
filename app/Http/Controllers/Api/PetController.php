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
    public function index($roomId): JsonResponse
    {
        try {
            $pets = Pet::with('photos')->where('room_id', $roomId)->get();
            return response()->json(['pets' => $pets], 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        try {
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

    public function show(Int $id): JsonResponse
    {
        try {
            $pet = Pet::findOrFail($id);

            return response()->json([
                'pet' => $pet
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Pet not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        try {
            $pet = Pet::find($id);

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
            return response()->json(['error' => 'Pet or room not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy($id): JsonResponse
    {
        $user = request()->user();

        try {
            $pet = Pet::findOrFail($id);

            $room = Room::findOrFail($pet->room_id);

            // Check permissions
            if ($resp = $this->checkShelterAccess($user, $room->shelter_id)) {
                return $resp;
            }

            $pet->delete();

            return response()->json(['message' => 'Pet deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pet or room not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function uploadPhoto(Request $request, $petId): JsonResponse
    {
        $user = $request->user();
        try {
            $pet = Pet::findOrFail($petId);
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

            return response()->json([
                'message' => 'Photo uploaded successfully',
                'data' => [
                    'id' => $photo->id,
                    'url' => asset('storage/' . $photo->file_path)
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid input data', 'details' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pet or room not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    private function checkShelterAccess($user, $shelterId): ?JsonResponse
    {
        $shelterId = (int) $shelterId;

        if ($user->role === 'admin') {
            return null;
        }

        if ($user->role === 'worker') {
            if ($user->shelter_id === $shelterId) {
                return null;
            }
            return response()->json(['error' => 'Forbidden: Not your shelter'], 403);
        }

        return response()->json(['error' => 'Forbidden: Insufficient permissions'], 403);
    }

    private function errorResponse(\Exception $e): JsonResponse
    {
        return response()->json([
            'error' => 'Server error',
            'message' => $e->getMessage()
        ], 500);
    }
}
