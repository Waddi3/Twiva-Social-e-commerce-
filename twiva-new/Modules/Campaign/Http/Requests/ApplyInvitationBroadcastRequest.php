<?php

namespace Modules\Campaign\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Campaign\Entities\Post;

class ApplyInvitationBroadcastRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'post_id' => ['required', Rule::exists('posts', 'id')->where('post_type', Post::BROADCAST)],
            'application' => ['required', 'string', 'max:512'],
            'influencer_amount' => ['nullable', 'min:1'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
