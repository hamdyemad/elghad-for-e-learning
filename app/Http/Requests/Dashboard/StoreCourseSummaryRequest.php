<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ];

        if ($this->isMethod('PUT')) {
            $rules['pdf'] = 'nullable|file|mimes:pdf|max:10240';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'العنوان مطلوب',
            'title.max' => 'العنوان يجب أن لا يتجاوز 255 حرف',
            'pdf.required' => 'ملف PDF مطلوب',
            'pdf.mimes' => 'يجب أن يكون الملف بصيغة PDF',
            'pdf.max' => 'حجم الملف يجب أن لا يتجاوز 10MB',
        ];
    }
}
