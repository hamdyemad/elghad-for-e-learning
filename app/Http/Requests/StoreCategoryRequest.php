<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم القسم مطلوب',
            'name.max' => 'اسم القسم يجب ألا يتجاوز 255 حرف',
            'slug.unique' => 'هذا الـ slug مستخدم بالفعل',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجا',
            'parent_id.exists' => 'القسم الرئيسي غير موجود',
            'parent_id.different' => 'لا يمكن تعيين القسم ك본 שלו كأب',
            'status.in' => 'الحالة يجب أن تكون active أو inactive',
            'order.min' => 'الترتيب يجب أن يكون رقم موجب'
        ];
    }
}
