<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Pet;

/**
 * @OA\Schema(
 *     schema="AdoptionRequest",
 *     required={"pet_id","applicant_name","email"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="pet_id", type="integer"),
 *     @OA\Property(property="applicant_name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="status", type="string", enum={"pending","approved","rejected"})
 * )
 */

class AdoptionRequest extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'applicant_name', 'email', 'status'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
