<?php

namespace Modules\Chat\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ApiTimelineReportStatusRequest extends FormRequest
{

    // protected function prepareForValidation()
    // {

    // }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => ['required', 'string', 'in:open,closed']
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
