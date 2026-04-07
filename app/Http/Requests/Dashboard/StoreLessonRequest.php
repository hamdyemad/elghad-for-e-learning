<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
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
            'topic' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'outsource_link' => 'nullable|string|max:2048',
            'outsource_type' => 'nullable|string|in:vimeo,firebase,vdocipher,youtube,other',
            'is_free' => 'boolean',
            'duration' => 'nullable|integer|min:0',
            'file_pdf' => 'nullable|file|mimes:mp4,mkv,avi,mov,wmv,flv,webm,pdf|max:51200',
            'course_id' => 'required|integer|exists:courses,id',
            'order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'topic.required' => 'الموضوع مطلوب',
            'topic.max' => 'الموضوع يجب أن لا يتجاوز 255 حرف',
            'title.required' => 'العنوان مطلوب',
            'title.max' => 'العنوان يجب أن لا يتجاوز 255 حرف',
            'outsource_link.max' => 'الرابط يجب أن لا يتجاوز 2048 حرف',
            'outsource_type.in' => 'نوع الرابط يجب أن يكون: vimeo, firebase, vdocipher, youtube, other',
            'duration.min' => 'المدة يجب أن تكون قيمة موجبة',
            'file_pdf.file' => 'الملف يجب أن يكون ملف صالح',
            'file_pdf.mimes' => 'صيغ الملف المسموح بها: mp4, mkv, avi, mov, wmv, flv, webm, pdf',
            'file_pdf.max' => 'حجم الملف يجب أن لا يتجاوز 50 ميجابايت',
            'course_id.required' => 'رقم الدورة مطلوب',
            'course_id.integer' => 'رقم الدورة يجب أن يكون رقم صحيح',
            'course_id.exists' => 'الدورة غير موجودة',
            'order.min' => 'الترتيب يجب أن يكون قيمة موجبة',
        ];
    }
}
