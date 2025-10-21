<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index($shelterId)
    {
        return response()->json(Room::where('shelter_id', $shelterId)->get());
    }

    public function show($shelterId, Room $room)
    {
        return response()->json($room); 
    }

    public function store(Request $request, $shelterId)
    {
        $room = Room::create(array_merge($request->all(), ['shelter_id' => $shelterId]));
        return response()->json($room, 201);
    }

    public function update(Request $request, Room $room)
    {
        $room->update($request->all());
        return response()->json($room);
    }

    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Shelter not found'], 404);
        }

        $room->delete();

        return response()->json(['message' => 'Shelter deleted successfully']);
    }
}
