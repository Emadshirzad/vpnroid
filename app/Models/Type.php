<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function sub()
    {
        return $this->hasMany(ListConfig::class);
    }
}
