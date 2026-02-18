<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinReportRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'report_title' => 'required|string|max:255',
            'customer' => 'nullable|string|max:255',
            'order_number' => 'nullable|string|max:255',
            'spk' => 'nullable|string|max:255',
            'spk_id' => 'nullable|exists:spks,id',
            'tz_count' => 'nullable|integer',
            'amount' => 'required|numeric',
            'received_amount' => 'nullable|numeric',
            'date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'nds_id' => 'nullable|exists:nds,id',
            'bonus_client' => 'nullable|numeric',
            'kickback' => 'nullable|numeric',
            'net_sales' => 'nullable|numeric',
            'remainder' => 'nullable|numeric',
            'manager_name' => 'nullable|string|max:255',
            'supplier_invoice_number' => 'nullable|string|max:255',
            'supplier_amount' => 'nullable|numeric',
            'payment_manager' => 'nullable|numeric',
            'payment_spk' => 'nullable|numeric',
            'sold_from' => 'nullable|string|max:255',
            'profit' => 'nullable|numeric',
            'markup' => 'nullable|numeric',
            'nds_percent' => 'nullable|numeric',
        ];
    }
}
