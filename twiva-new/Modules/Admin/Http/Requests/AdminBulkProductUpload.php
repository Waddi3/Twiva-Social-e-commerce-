<?php

namespace Modules\Admin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminBulkProductUpload extends FormRequest
{
    public function rules(): array
    {
        return [
            'business-id' => ['required'],
            'file' => ['required', 'mimes:zip'],
        ];
    }
}
