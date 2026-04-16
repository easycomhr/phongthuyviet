<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'type' => ['required', 'in:bug,feature,compliment,other'],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.max' => 'Họ tên không được vượt quá 100 ký tự.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 150 ký tự.',
            'type.required' => 'Vui lòng chọn loại góp ý.',
            'type.in' => 'Loại góp ý không hợp lệ.',
            'content.required' => 'Vui lòng nhập nội dung góp ý.',
            'content.min' => 'Nội dung góp ý phải có ít nhất 10 ký tự.',
            'content.max' => 'Nội dung góp ý không được vượt quá 2000 ký tự.',
            'website.max' => 'Yêu cầu không hợp lệ.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();
        if ($this->filled('website') && $errors->has('website') && count($errors->keys()) === 1) {
            throw new HttpResponseException(redirect()->route('feedback.create'));
        }

        parent::failedValidation($validator);
    }
}
