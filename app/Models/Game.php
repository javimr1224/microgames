<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Game extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'games';

    protected $fillable = [
        'name',
        'description',
        'image',
        'video',
        'file',
        'category',
        'recommended',
        'visits',
    ];

    public function getVisitsAttribute($value)
    {
        return $value ?? 0;
    }
}
