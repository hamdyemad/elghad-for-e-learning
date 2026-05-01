<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $phone = $this->phone;
            if (str_starts_with($phone, '218') || str_starts_with($phone, '+218')) {
                $this->merge([
                    'phone' => '0' . substr($phone, -9)
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check if email exists
                    $user = User::where('email', $value)->first();
                    if ($user) {
                        if (is_null($user->email_verified_at)) {
                            $fail('An account with this email already exists but has not been verified. Please check your email for the verification code or request a new one.');
                        } else {
                            $fail('An account with this email already exists. Please login or reset your password if you forgot it.');
                        }
                    }
                }
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^09[1245]\d{7}$/',
                'unique:users,phone'
            ],
            'type' => [
                'nullable',
                'string',
                'in:student,instructor,admin'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => 'name']),
            'email.required' => __('validation.required', ['attribute' => 'email']),
            'email.email' => __('validation.email', ['attribute' => 'email']),
            'password.required' => __('validation.required', ['attribute' => 'password']),
            'password.min' => __('validation.min.string', ['attribute' => 'password', 'min' => 8]),
            'password.confirmed' => __('validation.confirmed', ['attribute' => 'password']),
            'phone.required' => __('validation.required', ['attribute' => 'phone']),
            'phone.regex' => __('validation.regex', ['attribute' => 'phone']),
            'phone.unique' => __('validation.unique', ['attribute' => 'phone']),
            'type.in' => __('validation.in', ['attribute' => 'type']),
        ];
    }
}
