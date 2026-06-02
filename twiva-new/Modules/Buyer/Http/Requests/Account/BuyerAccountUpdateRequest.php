<?php

namespace Modules\Buyer\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BuyerAccountUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'class' => ['required', 'in:INDIVIDUAL,RETAILER'],
            'rep_name' => [
                Rule::requiredIf($this->class == 'RETAILER'),
                Rule::when($this->class == 'RETAILER', ['string', 'max:255']),
            ],
            'retail_store_name' => [
                Rule::requiredIf($this->class == 'RETAILER'),
                Rule::when($this->class == 'RETAILER', ['string', 'max:255']),
            ],
            'rep_mobile' => [
                Rule::requiredIf($this->type == 'RETAILER'),
                Rule::when($this->type == 'RETAILER', 'string'),
                Rule::when($this->type == 'RETAILER', 'phone:AUTO,KE')
            ],
        ];
    }
}
