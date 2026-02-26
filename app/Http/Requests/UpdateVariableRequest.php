<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVariableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:float,integer',
            'table_type' => 'required|string|in:company,fnc',
            'value' => 'required|string|min:1',
            'counteragent_type' => 'required|string|in:inn,ooo',
        ];
    }
}
