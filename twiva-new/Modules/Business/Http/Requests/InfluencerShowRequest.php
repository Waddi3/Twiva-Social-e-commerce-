<?php

namespace Modules\Business\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InfluencerShowRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => [ 'required','exists:users,id'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}