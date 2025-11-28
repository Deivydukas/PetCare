<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shelter;
use App\Models\AdoptionRequest;
use App\Models\Pet;

class AdminController extends Controller
{
    // Return counts for dashboard
    public function stats()
    {
        $totalUsers = User::count();
        $totalShelters = Shelter::count();
        $pendingAdoptions = AdoptionRequest::where('status', 'pending')->count();

        return response()->json([
            'users' => $totalUsers,
            'shelters' => $totalShelters,
            'pendingAdoptions' => $pendingAdoptions,
        ]);
    }
    // Return all pets for admin overview
    public function allPets()
    {
        try {
            // Attempt to fetch pets with room and shelter
            $pets = Pet::with(['room.shelter'])->get();

            if ($pets->isEmpty()) {
                return response()->json([
                    'status' => 'empty',
                    'message' => 'No pets found in the database.',
                    'pets_count' => 0
                ], 200);
            }

            return response()->json(['pets' => $pets], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Some related model not found.',
                'exception' => $e->getMessage(),
            ], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database query failed.',
                'exception' => $e->getMessage(),
                'sql' => $e->getSql(),        // shows the actual SQL query
                'bindings' => $e->getBindings() // shows bindings
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unexpected error occurred.',
                'exception' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }
}
