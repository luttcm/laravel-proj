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
        'spk_id',
        'supplier_id',
        'nds_id',
        'bonus_client',
        'kickback',
        'net_sales',
        'remainder',
        'manager_name',
        'supplier_invoice_number',
        'supplier_amount',
        'payment_manager',
        'payment_spk',
        'sold_from',
        'profit',
        'markup',
        'nds_percent',
    ];

    /**
     * Get the user that owns the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the SPK associated with the report.
     */
    public function spkPerson(): BelongsTo
    {
        return $this->belongsTo(Spk::class, 'spk_id');
    }

    /**
     * Get the supplier associated with the report.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the NDS associated with the report.
     */
    public function nds(): BelongsTo
    {
        return $this->belongsTo(Nds::class);
    }
}
