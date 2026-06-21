<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreLiveStreamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان البث مطلوب',
            'title.max' => 'عنوان البث يجب أن لا يتجاوز 255 حرف',
            'url.required' => 'رابط البث مطلوب',
            'url.url' => 'رابط البث يجب أن يكون رابطاً صالحاً',
            'url.max' => 'رابط البث يجب أن لا يتجاوز 500 حرف',
        ];
    }
}
