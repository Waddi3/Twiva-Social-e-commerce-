<?php

namespace Modules\Campaign\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyInvitationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'invitation_id' => ['required', 'exists:post_users,id'],
            'application' => ['required', 'string', 'max:512'],
            'influencer_amount' => ['nullable', 'min:1'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
