<?php

namespace Modules\Campaign\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class HireCampaignRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'post_id' => ['required', 'numeric'],
            'influencer_ids' => ['required', 'array']
        ];
    }
}
