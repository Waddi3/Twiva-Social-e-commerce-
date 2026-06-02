<?php

namespace Modules\Chat\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ApiTimelineReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => ['required', 'string', 'min:1', 'max:500'],
            'report_reason_id' => ['required', 'numeric', 'exists:report_reasons,id'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
