<?php

namespace Modules\User\Http\Requests\Redeem;

use Illuminate\Foundation\Http\FormRequest;

class UserRedeemReferralRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'points' => ['nullable'],
        ];
    }
}
