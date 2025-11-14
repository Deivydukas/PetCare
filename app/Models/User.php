<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'adress',
        'role',
        'shelter_id',
    ];
    // protected $attributes = [
    //     'status' => 'pending',
    // ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // // Patikrinti role
    // public function hasRole($role)
    // {
    //     return $this->role === $role;
    // }
    public function getJWTIdentifier()
    {
        return $this->getKey(); // grąžina user id
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'shelter_id' => $this->shelter_id,
        ];
    }
    public function worksAtShelter($shelterId)
    {
        return $this->role === 'worker' && $this->shelter_id == $shelterId;
    }
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Hash::make($value)
        );
    }
    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
