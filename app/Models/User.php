<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJwtIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJwtCustomClaims()
    {
        return [
            'role' => $this->role,
        ];
    }

    /**
     * Связь с таблицей картинок (одна картинка на пользователя)
     */
    public function picture()
    {
        return $this->hasOne(\App\Models\Picture::class, 'entity_id')
                    ->where('entity_type', 'user');
    }

    /**
     * Получить путь к аватару пользователя, возвращает дефолт если нет
     * Пути хранятся без ведущего слэша: 'avatars/classicAvatar.png' или 'storage/avatars/...'
     */
    public function getAvatarAttribute()
    {
        $pic = $this->picture()->first();
        if ($pic && $pic->path) {
            if (strpos($pic->path, 'storage/') === 0) {
                return '/' . $pic->path;
            }
            return '/storage/' . $pic->path;
        }

        return '/storage/avatars/classicAvatar.png';
    }
}

