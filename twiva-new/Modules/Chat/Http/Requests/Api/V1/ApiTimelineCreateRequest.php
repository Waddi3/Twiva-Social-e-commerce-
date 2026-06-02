<?php

namespace Modules\Chat\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ApiTimelineCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => ['required', 'string', 'min:1', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:4096'], // bmp,gif,svg,webp
            'chat_group_id' => ['nullable', 'exists:chat_groups,id'],
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
