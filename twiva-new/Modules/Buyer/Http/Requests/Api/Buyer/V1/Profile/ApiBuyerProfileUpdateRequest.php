<?php

namespace Modules\Buyer\Http\Requests\Api\Buyer\V1\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ApiBuyerProfileUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            // 'email' => '',
            // 'phone_code' => '',
            // 'phone_number' => '',
            'name' => ['string', 'min:1', 'max:100'],
            'class' => ['string', 'in:INDIVIDUAL,RETAILER'],
            'rep_name' => ['string', 'min:1', 'max:100'],
            'retail_store_name' => ['string', 'min:1', 'max:100'],
            'rep_phone_code' => ['string'],
            'rep_phone_number' => ['string'],
        ];
    }
}
