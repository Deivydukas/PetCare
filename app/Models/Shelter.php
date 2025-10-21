<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'email'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
