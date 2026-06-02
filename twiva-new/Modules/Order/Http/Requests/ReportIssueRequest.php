<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportIssueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'numeric'],
            'issue' => ['required'],
        ];
    }
}
