<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstructorRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:active,inactive',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'experience_years' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المحاضر مطلوب',
            'name.max' => 'اسم المحاضر يجب ألا يتجاوز 255 حرف',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني يجب أن يكون صالحًا',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين',
            'avatar.image' => 'الملف يجب أن يكون صورة',
            'avatar.mimes' => 'الصورة يجب أن تكون بصيغة: jpeg, png, jpg, gif',
            'avatar.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
            'status.in' => 'الحالة يجب أن تكون active أو inactive',
            'specialization.max' => 'التخصص يجب ألا يتجاوز 255 حرف',
            'hourly_rate.numeric' => 'معدل الساعة يجب أن يكون رقمًا',
            'hourly_rate.min' => 'معدل الساعة يجب أن يكون قيمة موجبة',
            'experience_years.integer' => 'سنوات الخبرة يجب أن تكون رقمًا صحيحًا',
            'experience_years.min' => 'سنوات الخبرة يجب أن تكون قيمة موجبة',
        ];
    }
}
