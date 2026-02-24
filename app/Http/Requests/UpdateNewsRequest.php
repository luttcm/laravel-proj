<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(auth()->user()->role, ['admin', 'redactor']);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'images' => 'nullable|array|max:9',
            'images.*' => 'nullable|image|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'images.max' => 'Максимум 9 картинок в новости',
        ];
    }
}
