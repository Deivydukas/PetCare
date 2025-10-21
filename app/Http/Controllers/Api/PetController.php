<?php

namespace App\Http\Controllers\Api;

use App\Models\Pet;
use App\Http\Resources\PetResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PetPhoto;
use App\Models\Room;

class PetController extends Controller
{
    public function index($roomId)
    {
        $pets = Pet::where('room_id', $roomId)->get();
        return response()->json($pets);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'species' => 'required|string|max:255',
                'breed' => 'nullable|string|max:255',
                'age' => 'nullable|integer',
                'status' => 'in:available,adopted',
                'room_id' => 'required|exists:rooms,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        }

        $room = Room::findOrFail($data['room_id']);

        // Count how many pets are currently in the room
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
    }

    public function show(Pet $pet)
    {
        return new PetResource($pet);
    }

    public function update(Request $request, Pet $pet)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'species' => 'sometimes|required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'status' => 'in:available,adopted',
        ]);

        $pet->update($data);

        return new PetResource($pet);
    }

    public function destroy(Pet $pet)
    {
        $pet->delete();

        return response()->noContent(); // 204
    }
    public function uploadPhoto(Request $request, $petId)
    {
        $pet = Pet::find($petId);

        if (!$pet) {
            return response()->json(['error' => 'Pet not found'], 404);
        }

        $request->validate([
            'photo' => 'required|image|max:5120' // max 5MB
        ]);

        $path = $request->file('photo')->store('pets', 'public');

        $photo = PetPhoto::create([
            'pet_id' => $pet->id,
            'file_path' => $path
        ]);

        return response()->json([
            'message' => 'Photo uploaded successfully',
            'data' => $photo
        ], 201);
    }
}
