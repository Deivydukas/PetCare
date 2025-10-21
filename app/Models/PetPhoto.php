<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetPhoto extends Model {
    use HasFactory;
    protected $fillable = ['pet_id','file_path'];
    public function pet(){ return $this->belongsTo(Pet::class); }
}

