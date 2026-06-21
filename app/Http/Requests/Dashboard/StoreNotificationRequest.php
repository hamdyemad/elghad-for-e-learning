<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'recipient_type' => 'required|in:all_students,all_instructors,single_student,single_instructor',
        ];

        if ($this->recipient_type === 'single_student' || $this->recipient_type === 'single_instructor') {
            $rules['recipient_id'] = 'required|integer|exists:users,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الإشعار مطلوب',
            'title.max' => 'عنوان الإشعار يجب أن لا يتجاوز 255 حرف',
            'body.required' => 'نص الإشعار مطلوب',
            'recipient_type.required' => 'نوع المستلم مطلوب',
            'recipient_type.in' => 'نوع المستلم غير صحيح',
            'recipient_id.required' => 'المستخدم المستهدف مطلوب',
            'recipient_id.exists' => 'المستخدم المستهدف غير موجود',
        ];
    }
}
