<?php

namespace Modules\Campaign\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ApiWaivePromptRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'post_id' => ['required', 'exists:posts,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
