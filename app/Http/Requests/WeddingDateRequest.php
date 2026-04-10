<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeddingDateRequest extends FormRequest
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
            'bride_name' => ['required', 'string', 'max:80'],
            'bride_birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'groom_name' => ['required', 'string', 'max:80'],
            'groom_birth_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'event_type' => ['required', 'in:engagement,wedding'],
            'target_year' => ['required', 'integer', 'between:1900,2100'],
            'target_month' => ['required', 'integer', 'between:1,12'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bride_name.required' => 'Vui lòng nhập tên cô dâu.',
            'bride_birth_date.required' => 'Vui lòng chọn ngày sinh cô dâu.',
            'bride_birth_date.date_format' => 'Ngày sinh cô dâu không đúng định dạng YYYY-MM-DD.',
            'groom_name.required' => 'Vui lòng nhập tên chú rể.',
            'groom_birth_date.required' => 'Vui lòng chọn ngày sinh chú rể.',
            'groom_birth_date.date_format' => 'Ngày sinh chú rể không đúng định dạng YYYY-MM-DD.',
            'event_type.required' => 'Vui lòng chọn loại nghi lễ.',
            'event_type.in' => 'Loại nghi lễ không hợp lệ.',
            'target_year.required' => 'Vui lòng chọn năm dự kiến cưới.',
            'target_year.between' => 'Năm dự kiến cưới cần nằm trong khoảng 1900-2100.',
            'target_month.required' => 'Vui lòng chọn tháng dự kiến cưới.',
            'target_month.between' => 'Tháng dự kiến cưới không hợp lệ.',
        ];
    }
}
