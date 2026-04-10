<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompatibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'person_a_gender'     => ['required', 'in:male,female'],
            'person_a_birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'person_b_gender'     => ['required', 'in:male,female'],
            'person_b_birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'person_a_gender.required'     => 'Vui lòng chọn giới tính người A.',
            'person_a_gender.in'           => 'Giới tính người A không hợp lệ.',
            'person_a_birth_date.required' => 'Vui lòng chọn ngày sinh người A.',
            'person_a_birth_date.date_format' => 'Ngày sinh người A phải đúng định dạng YYYY-MM-DD.',
            'person_a_birth_date.before_or_equal' => 'Ngày sinh người A không thể ở tương lai.',

            'person_b_gender.required'     => 'Vui lòng chọn giới tính người B.',
            'person_b_gender.in'           => 'Giới tính người B không hợp lệ.',
            'person_b_birth_date.required' => 'Vui lòng chọn ngày sinh người B.',
            'person_b_birth_date.date_format' => 'Ngày sinh người B phải đúng định dạng YYYY-MM-DD.',
            'person_b_birth_date.before_or_equal' => 'Ngày sinh người B không thể ở tương lai.',
        ];
    }
}
