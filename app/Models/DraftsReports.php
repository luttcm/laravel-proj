<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftsReports extends Model
{
    use HasFactory;

    protected $table = 'drafts_reports';

    protected $fillable = [
        'name',
        'amount',
        'manager_id',
        'date',
        'calculate_id',
    ];
}
