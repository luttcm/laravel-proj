<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property float $amount
 * @property int $manager_id
 * @property string $report_title
 * @property string $date
 * @property int $calculate_id
 */
class Reports extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'name',
        'amount',
        'manager_id',
        'report_title',
        'date',
        'calculate_id',
    ];
}
