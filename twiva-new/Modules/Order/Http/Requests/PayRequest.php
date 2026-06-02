<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class PayRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "merchant_id" => ['required', 'exists:transactions,transaction_id'],

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $keys = array_keys($validator->getMessageBag()->toArray());
        $response['errors'] = $validator->getMessageBag()->toArray();
        $response['errors_keys'] = $keys;
        return Response()->json($response, 400);
    }

}