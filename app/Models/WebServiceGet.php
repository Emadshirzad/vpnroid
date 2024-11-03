<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebServiceGet extends Model
{
    /** @use HasFactory<\Database\Factories\WebSrviceGetFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'key',
        'header',
        'is_encode',
        'service_id',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
