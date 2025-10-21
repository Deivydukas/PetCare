<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Pet;

class AdoptionRequest extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'applicant_name', 'email', 'status'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
