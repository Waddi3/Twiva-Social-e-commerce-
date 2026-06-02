<?php

namespace Modules\Platform\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WhatsAppGroupListRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'page' => is_null($this->page) ? 1 : max((int)$this->page, 1),
            'per_page' => is_null($this->per_page) ? 15 : (int)min(200, max(15, $this->per_page)),
            'sort_direction' => is_null($this->sort_direction) ? 'asc' : (in_array($this->sort_direction, ['asc', 'desc', 'ASC', 'DESC']) ? $this->sort_direction : 'asc'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => ['nullable', 'numeric', 'min:1'],
            'per_page' => ['nullable', 'numeric', 'min:15', 'max:200'],
            'query' => ['nullable', 'string', 'min:1'],
            'categories' => ['nullable', 'array'],
            'sort_by' => ['nullable', 'string', 'in:id,name,number_of_people,created_at'],
            'sort_direction' => ['nullable', 'string', 'in:desc,asc,DESC,ASC'],
            'min_number_of_people' => ['nullable', 'numeric'],
            'max_number_of_people' => ['nullable', 'numeric', 'gt:min_number_of_people'],
            'city_id' => ['nullable', 'numeric', 'exists:cities,id'],
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
