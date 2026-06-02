<?php

namespace Modules\Campaign\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use function auth;

class CreateBroadcastCampaignRequest extends FormRequest
{


    public function rules(): array
    {
        return [
            'post_name' => ['required'],
            'what_to_include' => ['nullable'],
            'description' => ['required'],
            'other_requirement' => ['nullable'],
            'price_per_post' => ['required', 'numeric'],
            'expiration_date' => ['required', 'date', 'after_or_equal:now'],
            'hashtag' => ['nullable'],
            'media' => ['nullable', 'array'],
            'media.*' => ['required', 'present', 'file', 'mimetypes:video/quicktime,video/mp4,video/ogg,image/jpeg,image/jpg,image/png,image/gif'],
        ];
    }

    public function authorize(): bool
    {
        return auth('api')->id();
    }
}
