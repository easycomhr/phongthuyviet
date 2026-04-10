<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchDayRequest extends FormRequest
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
            'category_slug' => ['required', 'string', 'exists:event_categories,slug'],
            'date'          => ['required', 'date_format:Y-m-d'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_slug.required' => 'Vui lòng chọn loại việc cần tra cứu.',
            'category_slug.exists'   => 'Loại việc không hợp lệ.',
            'date.required'          => 'Vui lòng chọn ngày tra cứu.',
            'date.date_format'       => 'Ngày tra cứu phải có định dạng YYYY-MM-DD.',
        ];
    }
}
