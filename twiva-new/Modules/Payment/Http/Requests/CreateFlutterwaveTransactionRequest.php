<?php

namespace Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Campaign\Entities\PostUser;
use Modules\Home\Entities\Transaction;

class CreateFlutterwaveTransactionRequest extends FormRequest
{
    protected function prepareForValidation()
    {
    }

    public function rules(): array
    {
        $rules = [
            'phone_number' => ['required', 'string'],
            'amount' => [['nullable', 'array', Rule::requiredIf(function () {
                return $this->request_type != Transaction::BUSINESS_PAY_FOR_CAMPAIGN;
            })]
                , 'numeric'],
            'request_type' => ['required', 'numeric', 'max:3'],
            'request_id' => ['required', 'numeric'],
            'details' => ['nullable', 'array', Rule::requiredIf(function () {
                return $this->request_type == Transaction::BUSINESS_PAY_FOR_CAMPAIGN;
            })]
        ];
        if ($this->request_type == Transaction::BUSINESS_PAY_FOR_CAMPAIGN)
        {
            $campaign_users = PostUser::where('post_id', $this->request_id)->get()->pluck('user_id')->toArray();
            $campaign_users = implode(',', $campaign_users);
            //id = influencer_id
            $rules['details.*.id'] = ['required', "in:$campaign_users"];
            $rules['details.*.price'] = ['required','numeric'];
        }
        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}