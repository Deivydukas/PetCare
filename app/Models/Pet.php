<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Pet",
 *     required={"name","species","room_id"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="species", type="string"),
 *     @OA\Property(property="breed", type="string"),
 *     @OA\Property(property="age", type="integer"),
 *     @OA\Property(property="status", type="string", enum={"available","adopted"}),
 *     @OA\Property(property="room_id", type="integer")
 * )
 */


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
}
