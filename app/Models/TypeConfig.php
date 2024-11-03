<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeConfig extends Model
{
    /** @use HasFactory<\Database\Factories\TypeConfigFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];
}
