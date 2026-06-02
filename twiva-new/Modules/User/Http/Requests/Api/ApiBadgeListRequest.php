<?php

namespace Modules\User\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ApiBadgeListRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->merge([
            'page' => is_null($this->page) ? 1 : max((int)$this->page, 1),
            'per_page' => is_null($this->per_page) ? 10 : (int)min(200, max(10, $this->per_page))
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => ['nullable', 'numeric', 'min:1'],
            'per_page' => ['nullable', 'numeric', 'min:10', 'max:200'],
            'query' => ['nullable', 'string', 'min:1', 'max:255'],
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
