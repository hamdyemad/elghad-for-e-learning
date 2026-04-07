<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الطالب مطلوب',
            'name.max' => 'اسم الطالب يجب ألا يتجاوز 255 حرف',
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
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف',
            'address.max' => 'العنوان يجب ألا يتجاوز 500 حرف',
            'date_of_birth.date' => 'تاريخ الميلاد يجب أن يكون تاريخًا صالحًا',
            'gender.in' => 'الجنس يجب أن يكون male أو female أو other',
        ];
    }
}
