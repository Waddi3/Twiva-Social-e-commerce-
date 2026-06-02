<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUploadShippingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'partner_id' => ['required'],
            'file' => ['required', ],
        ];
    }
}
