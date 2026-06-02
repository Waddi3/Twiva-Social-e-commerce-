<?php

namespace Modules\Chat\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ApiTimelineAddCommentsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => ['required', 'string', 'min:1', 'max:500'],
            'reply_id' => ['nullable', 'numeric', 'exists:comments,id'],
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
