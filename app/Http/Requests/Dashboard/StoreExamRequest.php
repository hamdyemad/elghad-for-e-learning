<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'is_active' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|size:4',
            'questions.*.options.*.option_text' => 'required|string',
            'questions.*.options.*.is_correct' => 'boolean',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الاختبار مطلوب',
            'title.max' => 'عنوان الاختبار يجب أن لا يتجاوز 255 حرف',
            'duration_minutes.integer' => 'مدة الاختبار يجب أن تكون رقماً',
            'duration_minutes.min' => 'مدة الاختبار يجب أن تكون على الأقل دقيقة واحدة',
            'passing_score.integer' => 'درجة النجاح يجب أن تكون رقماً',
            'passing_score.min' => 'درجة النجاح يجب أن تكون على الأقل 0',
            'passing_score.max' => 'درجة النجاح يجب أن لا تتجاوز 100',
            'questions.required' => 'يجب إضافة سؤال واحد على الأقل',
            'questions.min' => 'يجب إضافة سؤال واحد على الأقل',
            'questions.*.question.required' => 'نص السؤال مطلوب',
            'questions.*.options.required' => 'يجب إضافة 4 خيارات لكل سؤال',
            'questions.*.options.size' => 'يجب أن يكون هناك بالضبط 4 خيارات لكل سؤال',
            'questions.*.options.*.option_text.required' => 'نص الخيار مطلوب',
        ];
    }
}
