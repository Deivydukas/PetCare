<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthRecord;
use Illuminate\Http\Request;

class HealthRecordController extends Controller
{
    public function index()
    {
        return HealthRecord::with('pet', 'diseases')->get();
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'pet_id' => 'required|exists:pets,id',
                'description' => 'nullable|string',
                'last_checkup' => 'nullable|date',
                'notes' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        }

        $record = HealthRecord::create($data);
        return response()->json($record, 201);
    }

    public function show(HealthRecord $healthRecord)
    {
        return $healthRecord->load(['pet', 'diseases']);
    }

    public function update(Request $request, HealthRecord $healthRecord)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'last_checkup' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $healthRecord->update($validated);
        return response()->json($healthRecord, 200);
    }


    public function destroy(HealthRecord $healthRecord)
    {
        $healthRecord->delete();
        return response()->json(['message' => 'Health record deleted'], 204);
    }
}
