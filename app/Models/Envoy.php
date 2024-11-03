<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(
 *     schema="EnvoyModel",
 *     title="Envoy Model",
 *     description="Represents a envoy",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int32",
 *         description="user ID"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="username"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="name"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="password"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="phone envoy"
 *     ),
 *     @OA\Property(
 *         property="is_ban",
 *         type="boolean",
 *         description="is_ban"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="integer",
 *         description="address"
 *     ),
 *     @OA\Property(
 *         property="balance",
 *         type="string",
 *         description="balance"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="created date"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="updated date"
 *     ),
 * )
 */
class Envoy extends Model
{
    /** @use HasFactory<\Database\Factories\EnvoyFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'username',
        'password',
        'phone',
        'address',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password'          => 'hashed',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
