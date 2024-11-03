<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebServicePost extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'body',
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
