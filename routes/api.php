<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShelterController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\HealthRecordController;
use App\Http\Controllers\Api\DiseaseController;
use App\Http\Controllers\Api\AdoptionRequestController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;

//--- Autentifikacija ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


// --- Globos namai ---
Route::get('/shelters', [ShelterController::class, 'index']);
Route::get('/shelters/{shelter}', [ShelterController::class, 'show']);
Route::post('/shelters', [ShelterController::class, 'store']);
Route::put('/shelters/{shelter}', [ShelterController::class, 'update']);
Route::delete('/shelters/{shelter}', [ShelterController::class, 'destroy']);
Route::get('/shelters/{shelter}/pets', [ShelterController::class, 'getPets']); //Visi gyvūnai
Route::get('/shelters/{shelter}/rooms/{room}/pets', [ShelterController::class, 'getPetsFromRoom']); // Gyvūnai iš konkretaus kambario

// --- Kambariai ---
Route::get('/shelters/{shelter}/rooms', [RoomController::class, 'index']);
Route::get('/shelters/{shelter}/rooms/{room}', [RoomController::class, 'show']);
Route::post('/shelters/{shelter}/rooms', [RoomController::class, 'store']);
Route::put('/rooms/{room}', [RoomController::class, 'update']);
Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

// --- Gyvūnai ---
Route::get('/rooms/{room}/pets', [PetController::class, 'index']);
Route::get('/pets/{pet}', [PetController::class, 'show']);
Route::post('/pets', [PetController::class, 'store']);
Route::put('/pets/{pet}', [PetController::class, 'update']);
Route::delete('/pets/{pet}', [PetController::class, 'destroy']);
Route::post('/pets/{pet}/photos', [PetController::class, 'uploadPhoto']);

// --- Sveikatos įrašai ---
Route::apiResource('health-records', HealthRecordController::class);

// --- Ligos ---
Route::apiResource('diseases', DiseaseController::class);

// --- Įvaikinimo prašymai ---
Route::get('/adoptions', [AdoptionRequestController::class, 'index']);
Route::get('/adoptions/{adoption}', [AdoptionRequestController::class, 'show']);
Route::post('/adoptions', [AdoptionRequestController::class, 'store']);
Route::put('/adoptions/{adoption}', [AdoptionRequestController::class, 'update']);
Route::delete('/adoptions/{adoption}', [AdoptionRequestController::class, 'destroy']);

// --- Naudotojai ---
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);           // Get all users
    Route::get('/{id}', [UserController::class, 'show']);        // Get single user by ID
    Route::post('/', [UserController::class, 'store']);          // Create user
    Route::put('/{id}', [UserController::class, 'update']);      // Update user
    Route::delete('/{id}', [UserController::class, 'destroy']);  // Delete user
});

// --- Komentarai apie gyvūnus ---
Route::get('/comments/pet/{id}', [CommentController::class, 'index']);
Route::post('/comments', [CommentController::class, 'store']);
Route::get('/comments/{id}', [CommentController::class, 'show']);   
Route::put('/comments/{id}', [CommentController::class, 'update']);
Route::delete('/comments/{id}', [CommentController::class, 'destroy']); 

// Testinis endpointas
Route::get('/ping', function () {
    return response()->json(['message' => 'API veikia!']);
});







// // --- Public (no auth) ---
// Route::get('/ping', fn() => response()->json(['message' => 'API veikia!']));
// Route::get('/pets', [PetController::class, 'index']);
// Route::get('/pets/{pet}', [PetController::class, 'show']);
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// // // Quick debug route
// // Route::get('/test-role', function () {
// //     try {
// //         $middleware = app(RoleMiddleware::class);
// //         return response()->json([
// //             'success' => true,
// //             'message' => 'RoleMiddleware class exists and is autoloaded!',
// //             'class' => get_class($middleware),
// //         ]);
// //     } catch (\Throwable $e) {
// //         return response()->json([
// //             'success' => false,
// //             'message' => 'RoleMiddleware class NOT found',
// //             'error' => $e->getMessage(),
// //         ], 500);
// //     }
// // });
// Route::post('/logout', [AuthController::class, 'logout']);
// // --- Authenticated users ---
// Route::middleware('auth:sanctum')->group(function () {

//     Route::post('/logout', [AuthController::class, 'logout']);

//     // User routes
//     Route::get('/my-adoptions', [AdoptionRequestController::class, 'myAdoptions']);
//     Route::post('/adoptions', [AdoptionRequestController::class, 'store']);
//     Route::get('/adoptions/{adoption}', [AdoptionRequestController::class, 'show']);

//     // Worker & Admin routes
//     Route::middleware('role:worker,admin')->group(function () {
//         Route::post('/pets', [PetController::class, 'store']);
//         Route::put('/pets/{pet}', [PetController::class, 'update']);
//         Route::delete('/pets/{pet}', [PetController::class, 'destroy']);
//     });

//     // Admin-only routes
//     Route::middleware('role:admin')->group(function () {
//         Route::apiResource('/users', UserController::class)->except(['create','edit']);
//         Route::put('/adoptions/{adoption}', [AdoptionRequestController::class, 'update']);
//         Route::delete('/adoptions/{adoption}', [AdoptionRequestController::class, 'destroy']);
//     });
// });