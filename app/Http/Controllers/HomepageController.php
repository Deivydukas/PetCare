<?php

namespace App\Http\Controllers;

use App\Models\Pet;

class HomepageController extends Controller
{
    public function index()
    {
        $pets = Pet::select('name')->get();

        return view('home', [
            'pets' => $pets
        ]);
    }
}
