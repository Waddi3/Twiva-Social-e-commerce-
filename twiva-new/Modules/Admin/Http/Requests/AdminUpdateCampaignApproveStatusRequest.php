<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateCampaignApproveStatusRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'status' => 'required',
            'campaign_id' => ['required', 'numeric'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
