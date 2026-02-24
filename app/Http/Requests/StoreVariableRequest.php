<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVariableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:float,integer',
            'table_type' => 'required|string|in:company,fnc',
            'value' => 'required|string|min:1',
            'counteragent_type' => 'required|string|in:inn,ooo',
        ];
    }
}
