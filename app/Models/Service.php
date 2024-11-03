<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'type_id',
        'update_time',
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function channels()
    {
        return $this->hasMany(Channels::class);
    }

    public function webServiceGet()
    {
        return $this->hasMany(WebServiceGet::class);
    }

    public function webServicePost()
    {
        return $this->hasMany(WebServicePost::class);
    }

    public function subLink()
    {
        return $this->hasMany(LinkSub::class);
    }
}
