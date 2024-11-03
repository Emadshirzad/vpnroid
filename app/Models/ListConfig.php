<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListConfig extends Model
{
    /** @use HasFactory<\Database\Factories\ListConfigFactory> */
    use HasFactory;
    protected $fillable = [
        'type_id',
        'prepare',
        'prepare_id',
        'config'
    ];

    public function type()
    {
        return $this->belongsTo(TypeConfig::class);
    }

    public function sub()
    {
        return $this->belongsTo(Type::class, 'prepare');
    }

    public function check()
    {
        return $this->hasOne(CheckList::class);
    }
}
