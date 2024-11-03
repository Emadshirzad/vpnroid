<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkSub extends Model
{
    /** @use HasFactory<\Database\Factories\LinkSubFactory> */
    use HasFactory;
    protected $fillable = [
        'link',
        'service_id',
        'is_encode',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class); //Fixme
    }
}
