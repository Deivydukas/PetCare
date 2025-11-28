<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'species',
        'breed',
        'age',
        'status',
        'room_id',
    ];
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function healthRecords()
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function adoptionRequests()
    {
        return $this->hasMany(AdoptionRequest::class);
    }
    public function photos()
    {
        return $this->hasMany(PetPhoto::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function shelter()
    {
        return $this->belongsToThrough(Shelter::class);
    }
}
