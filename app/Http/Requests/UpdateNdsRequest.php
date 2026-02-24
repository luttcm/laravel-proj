<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNdsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'code_name' => 'required|string|max:50',
            'title' => 'required|string|max:50',
            'percent' => 'required|string|min:1',
        ];
    }
}
