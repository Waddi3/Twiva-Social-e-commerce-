<?php

namespace Modules\Payment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Home\Entities\Transaction;

class CheckPaymentStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'transaction_id' => ['required', 'string', 'exists:transactions,transaction_id']
        ];
    }

    public function authorize(): bool
    {
        return Transaction::where('transaction_id', request('transaction_id'))
                ->firstOrFail()
                ->user_id == auth('api')->id();
    }
}