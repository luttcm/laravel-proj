<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    use HasFactory;

    /**
     * Таблица в БД (миграция создала таблицу `picture`)
     */
    protected $table = 'picture';

    /**
     * Аттрибуты, которые можно назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'path',
        'entity_type',
        'entity_id',
    ];
}
