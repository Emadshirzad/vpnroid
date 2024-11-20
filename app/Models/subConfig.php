<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subConfig extends Model
{
    /** @use HasFactory<\Database\Factories\SubConfigFactory> */
    use HasFactory;
    protected $fillable = ['sub_id', 'config'];

    public function sub()
    {
        return $this->belongsTo(ListConfig::class);
    }
}
