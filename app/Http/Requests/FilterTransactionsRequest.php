<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterTransactionsRequest extends FormRequest
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
            'search' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:deposit,withdrawal',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'search.max' => 'كلمة البحث يجب ألا تتجاوز 255 حرف',
            'type.in' => 'النوع يجب أن يكون deposit أو withdrawal',
            'date_from.date' => 'تاريخ البداية يجب أن يكون تاريخاً صحيحاً',
            'date_to.date' => 'تاريخ النهاية يجب أن يكون تاريخاً صحيحاً',
            'date_to.after_or_equal' => 'تاريخ النهاية يجب أن يكون نفس تاريخ البداية أو بعده',
            'per_page.integer' => 'عدد العناصر يجب أن يكون رقماً صحيحاً',
            'per_page.min' => 'عدد العناصر يجب أن يكون على الأقل 1',
            'per_page.max' => 'عدد العناصر يجب ألا يتجاوز 100',
        ];
    }
}
