<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = [
        'avatar_url',
        'banner_url',
    ];

    protected $connection = 'mongodb';

    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'purchased_game_ids',
        'avatar',
        'banner',
        'bio',
    ];

    protected $attributes = [
        'role' => 'usuario',
        'purchased_game_ids' => [], // Ensure it defaults to an empty array
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function adminlte_profile_url()
    {
        return route('admin.profile.edit');
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return MediaStorage::url($this->avatar);
    }

    public function getBannerUrlAttribute(): ?string
    {
        return MediaStorage::url($this->banner);
    }
}
