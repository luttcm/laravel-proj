<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_title',
        'customer',
        'order_number',
        'spk',
        'tz_count',
        'amount',
        'received_amount',
        'date',
    ];

    /**
     * Get the user that owns the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
