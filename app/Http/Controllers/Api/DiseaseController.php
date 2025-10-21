<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{
    public function index()
    {
        return Disease::with('healthRecord')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'health_record_id' => 'required|exists:health_records,id',
            'name' => 'required|string|max:255',
            'treatment' => 'nullable|string',
        ]);

        $disease = Disease::create($data);
        return response()->json($disease, 201);
    }

    public function show(Disease $disease)
    {
        return $disease->load('healthRecord');
    }

    public function update(Request $request, Disease $disease)
    {
        $disease->update($request->all());
        return response()->json($disease, 200);
    }

    public function destroy(Disease $disease)
    {
        $disease->delete();
        return response()->json(['message' => 'Disease deleted'], 204);
    }
}
