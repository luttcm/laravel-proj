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
        'logistics_bonus',
        'fin_admin_bonus',
        'fbr_bonus',
    ];

    /**
     * Get the user that owns the report.
     * @return BelongsTo<User, FinReport>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the SPK associated with the report.
     * @return BelongsTo<Spk, FinReport>
     */
    public function spkPerson(): BelongsTo
    {
        return $this->belongsTo(Spk::class, 'spk_id');
    }

    /**
     * Get the supplier associated with the report.
     * @return BelongsTo<Supplier, FinReport>
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the NDS associated with the report.
     * @return BelongsTo<Nds, FinReport>
     */
    public function nds(): BelongsTo
    {
        return $this->belongsTo(Nds::class);
    }
}
