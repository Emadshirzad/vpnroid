<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckList extends Model
{
    /** @use HasFactory<\Database\Factories\CheckListFactory> */
    use HasFactory;

    protected $fillable = [
        'config_id',
        'operator_id',
        'healthy',
        'down',
    ];

    public function config()
    {
        return $this->belongsTo(ListConfig::class, 'config_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}
