<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Game extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'games';
}
