<?php

namespace Modules\Campaign\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use function auth;

class CancelCampaignRequest extends FormRequest
{


    public function rules(): array
    {
        return [
            'post_id' => ['required', 'numeric'],
            'influencer_id' => ['required', 'numeric']
        ];
    }

    public function authorize(): bool
    {
        return auth('api')->id();
    }
}
