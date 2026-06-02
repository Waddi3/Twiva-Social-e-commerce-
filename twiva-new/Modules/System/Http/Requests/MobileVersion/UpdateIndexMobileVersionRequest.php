<?php

namespace Modules\System\Http\Requests\MobileVersion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIndexMobileVersionRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        return $this->merge([
            'android_is_forced' => filter_var($this->android_is_forced, FILTER_VALIDATE_BOOLEAN),
            'ios_is_forced' => filter_var($this->ios_is_forced, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'android_id' => ['required', 'exists:mobile_versions,id'],
            'android_version' => ['required', 'string', 'min:1', 'max:30', 'regex:/^(\d+\.)?(\d+\.)?(\d+\.)?(\*|\d+)?$/'],
            'android_is_forced' => ['required', 'boolean'],
//            'android_store_link' => ['required', 'url'],

            'ios_id' => ['required', 'exists:mobile_versions,id'],
            'ios_version' => ['required', 'string', 'min:1', 'max:30', 'regex:/^(\d+\.)?(\d+\.)?(\d+\.)?(\*|\d+)?$/'],
            'ios_is_forced' => ['required', 'boolean'],
//            'ios_store_link' => ['required', 'url'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
