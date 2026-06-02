<?php

namespace Modules\Platform\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConnectPlatformRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'platform_id' => ['required', 'exists:platforms,id'],
            'token' => ['required', 'string'],
            'secret' => ['nullable', 'string']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}