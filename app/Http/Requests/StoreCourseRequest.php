<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'instructor_id' => 'required|integer|exists:users,id',
            'price' => 'nullable|numeric|min:0',
            'professor_profit' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:draft,published,public',
            'level' => 'nullable|string|max:100',
            'duration' => 'nullable|string|max:100',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الدورة مطلوب',
            'title.max' => 'عنوان الدورة يجب ألا يتجاوز 255 حرف',
            'description.string' => 'الوصف يجب أن يكون نصًا',
            'category_id.required' => 'القسم مطلوب',
            'category_id.integer' => 'القسم يجب أن يكون رقم صحيح',
            'category_id.exists' => 'القسم غير موجود',
            'instructor_id.required' => 'المحاضر مطلوب',
            'instructor_id.integer' => 'المحاضر يجب أن يكون رقم صحيح',
            'instructor_id.exists' => 'المحاضر غير موجود',
            'price.numeric' => 'السعر يجب أن يكون رقمًا',
            'price.min' => 'السعر يجب أن يكون قيمة موجبة',
            'professor_profit.numeric' => 'ربح المحاضر يجب أن يكون رقمًا',
            'professor_profit.min' => 'ربح المحاضر يجب أن يكون قيمة موجبة',
            'status.in' => 'الحالة يجب أن تكون draft أو published أو public',
            'level.max' => 'المستوى يجب ألا يتجاوز 100 حرف',
            'duration.max' => 'المدة يجب ألا تتجاوز 100 حرف',
            'thumbnail.image' => 'الملف يجب أن يكون صورة',
            'thumbnail.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجا',
        ];
    }
}
