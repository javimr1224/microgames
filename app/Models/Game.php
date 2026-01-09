<?php

namespace App\Models;

use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\Model;

class Game extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'games';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'video',
        'file',
        'category',
        'recommended',
        'visits',
        'price',
        'stripe_price_id',
    ];

    public function getVisitsAttribute($value)
    {
        return $value ?? 0;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($game) {
            $game->slug = Str::slug($game->name);
        });

        static::updating(function ($game) {
            if ($game->isDirty('name')) {
                $game->slug = Str::slug($game->name);
            }
        });
    }
}
