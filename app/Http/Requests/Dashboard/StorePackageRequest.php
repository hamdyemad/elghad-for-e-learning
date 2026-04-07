<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'integer|exists:courses,id',
            'status' => 'nullable|in:draft,published,public',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الباكج مطلوب',
            'title.max' => 'عنوان الباكج يجب ألا يتجاوز 255 حرف',
            'description.string' => 'الوصف يجب أن يكون نصًا',
            'category_id.required' => 'القسم مطلوب',
            'category_id.integer' => 'القسم يجب أن يكون رقم صحيح',
            'category_id.exists' => 'القسم غير موجود',
            'price.numeric' => 'السعر يجب أن يكون رقمًا',
            'price.min' => 'السعر يجب أن يكون قيمة موجبة',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.mimes' => 'الصورة يجب أن تكون بصيغة: jpeg, png, jpg, gif',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجا',
            'course_ids.array' => 'قائمة الدورات يجب أن تكون مصفوفة',
            'course_ids.*.integer' => 'معرف الدورة يجب أن يكون رقم صحيح',
            'course_ids.*.exists' => 'إحدى الدورات غير موجودة',
            'status.in' => 'الحالة يجب أن تكون draft أو published أو public',
        ];
    }
}
