<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string|null $name
 * @property string $email
 * @property string $role
 * @property string|null $google2fa_secret
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 */
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
        'google2fa_secret',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'google2fa_secret' => 'encrypted',
        'two_factor_confirmed_at' => 'datetime',
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
     * @return array<string, mixed>
     */
    public function getJwtCustomClaims()
    {
        return [
            'role' => $this->role,
        ];
    }

    /**
     * Связь с таблицей картинок (одна картинка на пользователя)
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Picture>
     */
    public function picture(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Picture::class, 'entity_id')
                    ->where('entity_type', 'user');
    }

    /**
     * Получить путь к аватару пользователя, возвращает дефолт если нет
     * Пути хранятся без ведущего слэша: 'avatars/classicAvatar.png' или 'storage/avatars/...'
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        $pic = $this->picture()->first();
        if ($pic && $pic->path) {
            if (strpos((string)$pic->path, 'storage/') === 0) {
                return '/' . $pic->path;
            }
            return '/storage/' . $pic->path;
        }

        return '/storage/avatars/classicAvatar.png';
    }
}

