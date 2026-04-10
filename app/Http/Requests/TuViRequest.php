<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TuViRequest extends FormRequest
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
            'gender' => ['required', 'in:male,female'],
            'birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gender.required' => 'Vui lòng chọn giới tính.',
            'gender.in' => 'Giới tính không hợp lệ.',
            'birth_date.required' => 'Vui lòng chọn ngày sinh.',
            'birth_date.date_format' => 'Ngày sinh phải đúng định dạng YYYY-MM-DD.',
            'birth_date.before_or_equal' => 'Ngày sinh không thể ở tương lai.',
        ];
    }
}

