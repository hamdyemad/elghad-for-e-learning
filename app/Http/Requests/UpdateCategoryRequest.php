<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $categoryId,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_id' => 'nullable|exists:categories,id|different:' . $categoryId,
            'order' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم القسم مطلوب',
            'name.max' => 'اسم القسم يجب ألا يتجاوز 255 حرف',
            'image.image' => 'الملف يجب أن يكون صورة',
            'image.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجا',
            'parent_id.exists' => 'القسم الرئيسي غير موجود',
            'status.in' => 'الحالة يجب أن تكون active أو inactive'
        ];
    }
}
