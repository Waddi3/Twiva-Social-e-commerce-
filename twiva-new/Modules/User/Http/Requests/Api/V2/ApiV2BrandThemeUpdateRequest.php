<?php

namespace Modules\User\Http\Requests\Api\V2;

use Illuminate\Foundation\Http\FormRequest;

class ApiV2BrandThemeUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "voice" => ['nullable', 'string', 'min:1', 'max:255'],
            "tone" => ['nullable', 'string', 'min:1', 'max:255'],
            "style" => ['nullable', 'string', 'min:1', 'max:255'],
            "structure" => ['nullable', 'string', 'min:1', 'max:255'],
            "location" => ['nullable', 'string', 'min:1', 'max:255'],
            "city_id" => ['nullable', 'numeric', 'exists:cities,id'],
            'latitude' => ['nullable', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'min:-180', 'max:180'],
            "min_age" => ['nullable', 'numeric', 'min:1', 'max:100'],
            "max_age" => ['nullable', 'numeric', 'min:1', 'max:100', 'gt:min_age'],
            "gender" => ['nullable', 'string', 'in:all,male,female'],
            "language_ids" => ['nullable', 'array',],
            "language_ids.*" => ['nullable', 'numeric', 'exists:languages,id'],
            "interest_ids" => ['nullable', 'array'],
            "interest_ids.*" => ['nullable', 'numeric', 'exists:interests,id'],
            "interest_list" => ['nullable', 'array'],
            "interest_list.*" => ['nullable', 'string', 'min:0', 'max:100'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
