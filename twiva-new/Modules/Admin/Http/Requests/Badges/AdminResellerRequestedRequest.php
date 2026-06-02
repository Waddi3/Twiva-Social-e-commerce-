<?php

namespace Modules\Admin\Http\Requests\Badges;

use Illuminate\Foundation\Http\FormRequest;

class AdminResellerRequestedRequest extends FormRequest
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
            'badge_user_id' => ['nullable', 'numeric', 'exists:badge_users,id'],
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
