<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="PetPhoto",
 *     required={"pet_id","file_path"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="pet_id", type="integer"),
 *     @OA\Property(property="file_path", type="string")
 * )
 */

class PetPhoto extends Model
{
    use HasFactory;
    protected $fillable = ['pet_id', 'file_path'];
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
