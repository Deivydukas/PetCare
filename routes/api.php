<?php

use Tymon\JWTAuth\Facades\JWTAuth;
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

// --- Public Access (guest) ---
Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/shelters',                       [ShelterController::class, 'index']);
Route::get('/shelters/{shelter}/rooms', [ShelterController::class, 'getRooms']);
Route::get('/shelters/{shelter}',            [ShelterController::class, 'show']);
Route::get('/shelters/{shelter}/rooms',       [RoomController::class, 'index']);
Route::get('/shelters/{shelter}/rooms/{room}', [RoomController::class, 'show']);
// Route::get('/rooms/{room}/pets',              [PetController::class, 'index']);
Route::get('/pets/{pet}',                     [PetController::class, 'show']);
Route::get('/shelters/{shelter}/pets',        [ShelterController::class, 'getPets']);
Route::get('/rooms/{room}/pets', [RoomController::class, 'pets']);
Route::get('/shelters/{shelter}/rooms/{room}/pets', [ShelterController::class, 'getPetsFromRoom']);
Route::get('/health-records',                 [HealthRecordController::class, 'index']);
Route::get('/diseases',                       [DiseaseController::class, 'index']);
Route::get('/comments/pet/{id}',              [CommentController::class, 'index']);
// Route::get('/pets/{pet}/photos',              [PetController::class, 'index']);

Route::group(['middleware' => 'jwt.cookie', 'jwt.auth'], function () {
    Route::middleware('role:user,worker,admin')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',     [AuthController::class, 'me']);
        // Adoption requests
        Route::post('/adoptions',                [AdoptionRequestController::class, 'store']);
        Route::get('/adoptions/{adoption}',      [AdoptionRequestController::class, 'show']);
        Route::put('/adoptions/{adoption}',      [AdoptionRequestController::class, 'update']);

        // Comments
        Route::post('/comments',                 [CommentController::class, 'store']);
        Route::put('/comments/{id}',             [CommentController::class, 'update']);
        Route::delete('/comments/{id}',          [CommentController::class, 'destroy']);

        // User account
        Route::put('/users/{id}',                [UserController::class, 'update']);
    });

    Route::middleware('role:worker,admin')->group(function () {

        // Rooms
        Route::post('/shelters/{shelter}/rooms',  [RoomController::class, 'store']);
        Route::put('/rooms/{room}',               [RoomController::class, 'update']);
        Route::delete('/rooms/{room}',            [RoomController::class, 'destroy']);

        // Pets
        Route::post('/pets',                      [PetController::class, 'store']);
        Route::put('/pets/{pet}',                 [PetController::class, 'update']);
        Route::delete('/pets/{pet}',              [PetController::class, 'destroy']);
        Route::post('/pets/{pet}/photos',         [PetController::class, 'uploadPhoto']);

        // Mark pet as adopted
        Route::put('/pets/{pet}/status',          [PetController::class, 'markAsAdopted']);

        // Adoption requests
        Route::get('/adoptions',                  [AdoptionRequestController::class, 'index']);
    });

    Route::middleware('role:admin')->group(function () {

        // Shelters
        Route::post('/shelters',             [ShelterController::class, 'store']);
        Route::put('/shelters/{shelter}',    [ShelterController::class, 'update']);
        Route::delete('/shelters/{shelter}', [ShelterController::class, 'destroy']);

        // Users management
        Route::get('/users',                 [UserController::class, 'index']);
        Route::post('/users',                [UserController::class, 'store']);
        Route::delete('/users/{id}',         [UserController::class, 'destroy']);

        // Adoption moderation
        Route::put('/adoptions/{adoption}/review', [AdoptionRequestController::class, 'review']);

        // Moderation tools
        Route::delete('/comments/{id}/force',      [CommentController::class, 'forceDelete']);
        Route::put('/users/{id}/role',             [UserController::class, 'changeRole']);
    });
});
