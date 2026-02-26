<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $type
 * @property string $value
 * @property string $table_type
 * @property string $counteragent_type
 */
class Variable extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "title",
        "type",
        "value",
        "table_type",
        "counteragent_type",
    ];
}
