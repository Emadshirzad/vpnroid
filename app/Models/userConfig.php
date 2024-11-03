<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userConfig extends Model
{
    /** @use HasFactory<\Database\Factories\UserConfigFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'config_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function config()
    {
        return $this->belongsTo(ListConfig::class);
    }
}
