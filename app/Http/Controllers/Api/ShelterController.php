<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shelter;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ShelterController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $shelters = Shelter::with('rooms.pets')->get();
            return response()->json($shelters, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
            ]);
            $shelter = Shelter::create($validated);
            return response()->json([
                'message' => 'Shelter created successfully',
                'data' => $shelter
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $shelter = Shelter::with('rooms')->findOrFail($id);
            return response()->json($shelter, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shelter not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $shelter = Shelter::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'address' => 'sometimes|string|max:255',
                'phone' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
            ]);

            $shelter->update($validated);

            return response()->json([
                'message' => 'Shelter updated successfully',
                'data' => $shelter
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shelter not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $shelter = Shelter::findOrFail($id);
            $shelter->delete();

            return response()->json(['message' => 'Shelter deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shelter not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function getRooms($shelterId): JsonResponse
    {
        try {
            $shelter = Shelter::with('rooms.pets')->findOrFail($shelterId);

            return response()->json([
                'shelter' => $shelter->name,
                'rooms' => $shelter->rooms
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shelter not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function getPets($shelterId): JsonResponse
    {
        try {
            $shelter = Shelter::with('rooms.pets')->findOrFail($shelterId);
            $pets = $shelter->rooms->flatMap->pets;

            return response()->json([
                'shelter' => $shelter->name,
                'total_pets' => $pets->count(),
                'pets' => $pets->values()
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shelter not found'], 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function getPetsFromRoom($shelterId, $roomId): JsonResponse
    {
        try {
            $shelter = Shelter::findOrFail($shelterId);

            $room = Room::where('id', $roomId)
                ->where('shelter_id', $shelterId)
                ->with('pets')
                ->firstOrFail();

            return response()->json([
                'shelter' => $shelter->name,
                'room' => $room->name,
                'total_pets' => $room->pets->count(),
                'pets' => $room->pets
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shelter or Room not found'], 404);
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
