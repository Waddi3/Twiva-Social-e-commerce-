<?php

namespace Modules\Platform\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexStoryPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'numeric | nullable',
            'is_schedule' => 'boolean | required',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}