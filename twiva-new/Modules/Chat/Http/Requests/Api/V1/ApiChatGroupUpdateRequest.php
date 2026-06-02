<?php

namespace Modules\Chat\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ApiChatGroupUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,png,jpeg', 'max:4096'], // bmp,gif,svg,webp
            'description' => ['required', 'string', 'min:1', 'max:500'],
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
