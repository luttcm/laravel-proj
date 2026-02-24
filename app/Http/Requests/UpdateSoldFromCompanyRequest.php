<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSoldFromCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:sold_from_companies,name,' . $this->route('id'),
        ];
    }
}
