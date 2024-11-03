<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channels extends Model
{
    /** @use HasFactory<\Database\Factories\ChannelsFactory> */
    use HasFactory;
    protected $fillable = [
        'link',
        'is_encode',
        'service_id',
    ];

    public function config()
    {
        return $this->hasMany(Config::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
