<?php

namespace Modules\Admin\Http\Requests\Badges;

use Illuminate\Foundation\Http\FormRequest;

class AdminResellerUpdateStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'numeric', 'exists:badge_users,id'],
            'status' => ['required', 'string', 'in:approved,declined'],
            'comments' => ['nullable', 'string', 'min:1', 'max:255'],
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
