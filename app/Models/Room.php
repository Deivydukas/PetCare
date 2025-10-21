<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['shelter_id', 'name', 'capacity'];

    public function shelter()
    {
        return $this->belongsTo(Shelter::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
