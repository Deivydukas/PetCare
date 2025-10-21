<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shelter;
use App\Models\Room;
use Illuminate\Http\Request;

class ShelterController extends Controller
{
    /**
     * Display a listing of all shelters with their rooms and pets.
     */
    public function index()
    {
        $shelters = Shelter::with('rooms.pets')->get();
        return response()->json($shelters);
    }

    /**
     * Store a newly created shelter in storage.
     */
    public function store(Request $request)
    {
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
    }

    /**
     * Display the specified shelter with its rooms and pets.
     */
    public function show($id)
    {
        $shelter = Shelter::with('rooms.pets')->find($id);

        if (!$shelter) {
            return response()->json(['message' => 'Shelter not found'], 404);
        }

        return response()->json($shelter);
    }

    /**
     * Update the specified shelter in storage.
     */
    public function update(Request $request, $id)
    {
        $shelter = Shelter::find($id);

        if (!$shelter) {
            return response()->json(['message' => 'Shelter not found'], 404);
        }

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
        ]);
    }

    /**
     * Remove the specified shelter from storage.
     */
    public function destroy($id)
    {
        $shelter = Shelter::find($id);

        if (!$shelter) {
            return response()->json(['message' => 'Shelter not found'], 404);
        }

        $shelter->delete();

        return response()->json(['message' => 'Shelter deleted successfully']);
    }

    /**
     * Get all rooms in a specific shelter.
     */
    public function getRooms($shelterId)
    {
        $shelter = Shelter::with('rooms.pets')->find($shelterId);

        if (!$shelter) {
            return response()->json(['message' => 'Shelter not found'], 404);
        }

        return response()->json([
            'shelter' => $shelter->name,
            'rooms' => $shelter->rooms
        ]);
    }

    /**
     * Get all pets across all rooms in a shelter.
     */
    public function getPets($shelterId)
    {
        $shelter = Shelter::with('rooms.pets')->find($shelterId);

        if (!$shelter) {
            return response()->json(['message' => 'Shelter not found'], 404);
        }

        $pets = $shelter->rooms->flatMap->pets;

        return response()->json([
            'shelter' => $shelter->name,
            'total_pets' => $pets->count(),
            'pets' => $pets->values()
        ]);
    }
    /**
     * Get all pets from a specific room in a specific shelter.
     */
    public function getPetsFromRoom($shelterId, $roomId)
    {
        // Patikriname ar shelter egzistuoja
        $shelter = Shelter::find($shelterId);
        if (!$shelter) {
            return response()->json(['message' => 'Shelter not found'], 404);
        }

        // Patikriname ar room egzistuoja ir priklauso šiam shelter
        $room = Room::where('id', $roomId)
            ->where('shelter_id', $shelterId)
            ->with('pets')
            ->first();

        if (!$room) {
            return response()->json(['message' => 'Room not found in this shelter'], 404);
        }

        // Grąžiname rezultatą
        return response()->json([
            'shelter' => $shelter->name,
            'room' => $room->name,
            'total_pets' => $room->pets->count(),
            'pets' => $room->pets
        ]);
    }
}
