<?php

namespace Modules\System\Http\Requests\MobileVersion;

use Illuminate\Foundation\Http\FormRequest;

class StoreMobileVersionRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        return $this->merge([
            'is_forced' => filter_var($this->is_forced, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'min:1', 'max:50'],
            'platform' => ['required', 'string', 'min:1', 'max:20'],
            'version' => ['required', 'string', 'min:1', 'max:30'],
            'is_forced' => ['required', 'boolean'],
            'link' => ['required', 'url'],
            'store_link' => ['required', 'url'],
            'features' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
