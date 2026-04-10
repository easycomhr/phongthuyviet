<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BabyNamingRequest extends FormRequest
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
            'father_name' => ['required', 'string', 'max:80'],
            'father_birth_year' => ['required', 'integer', 'between:1900,2100'],
            'mother_name' => ['required', 'string', 'max:80'],
            'mother_birth_year' => ['required', 'integer', 'between:1900,2100'],
            'baby_gender' => ['required', 'in:male,female'],
            'baby_birth_date' => ['required', 'date_format:Y-m-d H:i'],
            'include_mother_surname' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'father_name.required' => 'Vui lòng nhập tên bố.',
            'father_birth_year.required' => 'Vui lòng nhập năm sinh của bố.',
            'mother_name.required' => 'Vui lòng nhập tên mẹ.',
            'mother_birth_year.required' => 'Vui lòng nhập năm sinh của mẹ.',
            'baby_gender.required' => 'Vui lòng chọn giới tính bé.',
            'baby_birth_date.required' => 'Vui lòng chọn ngày giờ sinh của bé.',
            'baby_birth_date.date_format' => 'Ngày giờ sinh phải đúng định dạng YYYY-MM-DD HH:mm.',
        ];
    }
}

