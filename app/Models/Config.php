<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ConfigModel",
 *     title="Config Model",
 *     description="Represents a config",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int32",
 *         description="config ID"
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         description="url"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="type"
 *     ),
 * )
 */
class Config extends Model
{
    /** @use HasFactory<\Database\Factories\ConfigFactory> */
    use HasFactory;

    protected $fillable = ['url', 'type', 'channels_id'];

    public function channel()
    {
        return $this->belongsTo(Channels::class);
    }
}
