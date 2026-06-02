<?php

namespace Modules\Campaign\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeclineInvitationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'invitation_id' => ['required', 'exists:post_users,id'],

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}