<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstructorRequest extends FormRequest
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
            'password' => 'nullable|string|min:6',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'experience_years' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المدرب مطلوب',
            'name.max' => 'اسم المدرب يجب ألا يتجاوز 255 حرف',
            'email.email' => 'البريد الإلكتروني يجب أن يكون صالحًا',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'specialization.max' => 'التخصص يجب ألا يتجاوز 255 حرف',
            'hourly_rate.numeric' => 'معدل الساعة يجب أن يكون رقمًا',
            'hourly_rate.min' => 'معدل الساعة يجب أن يكون قيمة موجبة',
            'experience_years.integer' => 'سنوات الخبرة يجب أن تكون رقم صحيح',
            'experience_years.min' => 'سنوات الخبرة يجب أن تكون قيمة موجبة',
            'status.in' => 'الحالة يجب أن تكون active أو inactive',
            'avatar.image' => 'الملف يجب أن يكون صورة',
            'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجا',
        ];
    }
}
