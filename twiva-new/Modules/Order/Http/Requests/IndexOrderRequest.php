<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => ['in:Placed,Processing,Delivered'],
            'page' => ['nullable', 'numeric'],
            'keyword' => ['nullable', 'string'],

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}