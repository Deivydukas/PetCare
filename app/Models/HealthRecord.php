<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pet;
use App\Models\Disease;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'description', 'last_checkup', 'notes'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function diseases()
    {
        return $this->hasMany(Disease::class);
    }
}
