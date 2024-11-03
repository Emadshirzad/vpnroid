<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * @OA\Schema(
 *     schema="UserModel",
 *     title="User Model",
 *     description="Represents a user",
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
 *         property="password",
 *         type="string",
 *         description="password"
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         type="string",
 *         description="created by envoy"
 *     ),
 *     @OA\Property(
 *         property="is_ban",
 *         type="boolean",
 *         description="is_ban"
 *     ),
 *     @OA\Property(
 *         property="subscription_duration",
 *         type="integer",
 *         description="subscription_duration"
 *     ),
 *     @OA\Property(
 *         property="last_purchase_date",
 *         type="string",
 *         format="date-time",
 *         description="last purchase date"
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
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'subscription_duration',
        'last_purchase_date',
        'envoy_id',
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
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function envoy()
    {
        return $this->belongsTo(Envoy::class);
    }

    public function hasEnvoy()
    {
        return $this->hasOne(Envoy::class);
    }

    public function userConfigs()
    {
        return $this->hasMany(UserConfig::class);
    }
}
