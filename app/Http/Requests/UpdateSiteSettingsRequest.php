<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
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
            'facebook' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:500',
            'tiktok' => 'nullable|string|max:500',
            'mobile_number' => 'nullable|string|max:500',
            'terms_of_use' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'facebook.max' => 'رابط Facebook يجب أن لا يتجاوز 500 حرف',
            'instagram.max' => 'رابط Instagram يجب أن لا يتجاوز 500 حرف',
            'tiktok.max' => 'رابط TikTok يجب أن لا يتجاوز 500 حرف',
            'mobile_number.max' => 'رقم الجوال يجب أن لا يتجاوز 500 حرف',
        ];
    }
}
