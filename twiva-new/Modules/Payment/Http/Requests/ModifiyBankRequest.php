<?php

namespace Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModifiyBankRequest extends FormRequest
{
    public function rules(): array
    {
        return [

            "beneficiary_name" => ['required',],
            "account_type" => ['required',],
            "account_number" => ['required', 'string'],
            "address" => ['required'],
            "beneficiary_city" => ['required', 'string'],
            "beneficiary_country_id" => ['required', 'exists:countries,id'],
            "bank_id" => ['required', 'exists:banks,id'],
            "bank_city" => ['required', 'string'],
            "bank_country_id" => ['required', 'exists:countries,id'],
            "bank_branch_name" => ['required']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}