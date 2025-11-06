<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Room",
 *     required={"name","shelter_id"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="capacity", type="integer"),
 *     @OA\Property(property="shelter_id", type="integer")
 * )
 */

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
