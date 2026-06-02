<?php

namespace Modules\Platform\Http\Requests\Api\V2;

use Illuminate\Foundation\Http\FormRequest;

class ApiV2StoryPostIndexRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'page' => is_null($this->page) ? 1 : max((int)$this->page, 1),
            'per_page' => is_null($this->per_page) ? 15 : (int)min(200, max(15, $this->per_page)),
            // 'is_schedule' => filter_var($this->input('is_schedule', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function rules(): array
    {
        return [
            'page' => ['nullable', 'numeric'],
            'per_page' => ['nullable', 'numeric'],
            'type' => ['nullable', 'string', 'in:scheduled,posted'],
            'query' => ['nullable', 'string', 'min:1', 'max:255'],
        ];
    }
    public function authorize(): bool
    {
        return true;
    }
}
