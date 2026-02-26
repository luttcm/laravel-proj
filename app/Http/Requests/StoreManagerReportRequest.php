<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManagerReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'report_name' => 'nullable|string|max:255',
            'buying_name' => 'nullable|string|max:255',
            'date' => 'nullable|string',
            'selling_name' => 'nullable|string|max:255',
            'spk' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'purchase_sum' => 'nullable|numeric',
            'markup_percent' => 'nullable|numeric',
            'selling_price' => 'nullable|numeric',
            'selling_sum' => 'nullable|numeric',
            'prf_percent' => 'nullable|numeric',
            'deal_payment' => 'nullable|numeric',
            'per_unit_payment' => 'nullable|numeric',
            'in_the_hand' => 'nullable|numeric',
            'nds_id' => 'nullable|exists:nds,id',
            'spk_id' => 'nullable|exists:spks,id',
            'calculation_id' => 'nullable|integer',
            'report_id' => 'nullable|integer',
        ];
    }
}
