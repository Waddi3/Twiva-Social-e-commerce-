<?php

namespace Modules\Chat\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Entities\User;

class ApiChatGroupAddMembersRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'chat_group_id' => ['required', 'exists:chat_groups,id'],
            'user_ids' => ['required', 'array'],
            'user_ids.*' => [
                'required',
                Rule::exists('users', 'id')
                    ->where('type', User::InfluencerType)
                    ->whereNull('banned_at')
                    ->whereNull('deleted_at')
            ],
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
