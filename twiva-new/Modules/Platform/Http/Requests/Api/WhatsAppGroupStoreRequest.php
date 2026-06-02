<?php

namespace Modules\Platform\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WhatsAppGroupStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'whats_app_groups' => ['required', 'array'],
            'whats_app_groups.*.name' => ['required', 'string'],
            'whats_app_groups.*.number_of_people' => ['required', 'numeric', 'min:1', 'max:65535'],
            'whats_app_groups.*.city_id' => ['required', 'exists:cities,id'],
            'whats_app_groups.*.invite_link' => ['required', 'url'],
            'whats_app_groups.*.category_ids' => ['required', 'array'],
            'whats_app_groups.*.category_ids.*' => ['required', 'exists:whats_app_categories,id'],
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
