<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    /** @use HasFactory<\Database\Factories\OperatorFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'limit_number',
    ];

    public function checkList()
    {
        return $this->hasOne(CheckList::class);
    }
}
