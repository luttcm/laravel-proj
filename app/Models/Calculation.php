<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calculation extends Model
{
    protected $fillable = [
        'user_id',
        'buying_name',
        'selling_name',
        'spk',
        'purchase_price',
        'quantity',
        'purchase_sum',
        'markup_percent',
        'selling_price',
        'selling_sum',
        'prf_percent',
        'deal_payment',
        'per_unit_payment',
        'manager_payment',
        'manager_salary_brutto',
        'in_the_hand',
        'in_the_deal',
        'in_the_deal_sum',
        'in_the_hand_sum',
        'nds_id',
        'spk_id',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'purchase_sum' => 'decimal:2',
        'markup_percent' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'selling_sum' => 'decimal:2',
        'prf_percent' => 'decimal:2',
        'deal_payment' => 'decimal:2',
        'per_unit_payment' => 'decimal:2',
        'manager_payment' => 'decimal:2',
        'manager_salary_brutto' => 'decimal:2',
        'in_the_hand' => 'decimal:2',
        'in_the_deal' => 'decimal:2',
        'in_the_deal_sum' => 'decimal:2',
        'in_the_hand_sum' => 'decimal:2',
        'nds_id' => 'integer',
    ];

    /**
     * Get the user that owns the calculation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
