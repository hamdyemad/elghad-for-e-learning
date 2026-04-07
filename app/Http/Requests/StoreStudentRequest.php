<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'enrollment_date' => 'nullable|date',
            'status' => 'nullable|in:active,inactive',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الطالب مطلوب',
            'name.max' => 'اسم الطالب يجب ألا يتجاوز 255 حرف',
            'email.email' => 'البريد الإلكتروني يجب أن يكون صالحًا',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف',
            'date_of_birth.date' => 'تاريخ الميلاد يجب أن يكون تاريخًا صالحًا',
            'enrollment_date.date' => 'تاريخ التسجيل يجب أن يكون تاريخًا صالحًا',
            'status.in' => 'الحالة يجب أن تكون active أو inactive',
            'avatar.image' => 'الملف يجب أن يكون صورة',
            'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجا',
        ];
    }
}
