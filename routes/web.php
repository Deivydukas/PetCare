<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomepageController;

Route::get('/', [HomepageController::class, 'index']);
