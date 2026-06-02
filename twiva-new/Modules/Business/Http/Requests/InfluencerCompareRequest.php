<?php

namespace Modules\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InfluencerCompareRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => ['array', 'required','min:2'],
            'ids.*' => ['required', 'exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}