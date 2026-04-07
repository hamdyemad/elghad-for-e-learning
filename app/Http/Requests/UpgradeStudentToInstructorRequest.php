<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeStudentToInstructorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'experience_years' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'specialization.max' => 'التخصص يجب ألا يتجاوز 255 حرف',
            'bio.string' => 'السيرة الذاتية يجب أن تكون نصًا',
            'hourly_rate.numeric' => 'معدل الساعة يجب أن يكون رقمًا',
            'hourly_rate.min' => 'معدل الساعة يجب أن يكون قيمة موجبة',
            'experience_years.integer' => 'سنوات الخبرة يجب أن تكون رقم صحيح',
            'experience_years.min' => 'سنوات الخبرة يجب أن تكون قيمة موجبة',
        ];
    }
}
