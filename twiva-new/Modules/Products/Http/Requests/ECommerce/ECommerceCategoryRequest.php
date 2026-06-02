<?php

namespace Modules\Products\Http\Requests\ECommerce;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

class ECommerceCategoryRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        // $this->merge([
        //     'sort_direction' => is_null($this->sort_direction) ? 'asc' : (in_array($this->sort_direction, ['asc', 'desc', 'ASC', 'DESC']) ? $this->sort_direction : 'asc'),
        //     'page' => is_null($this->page) ? 1 : max((int)$this->page, 1),
        //     'per_page' => is_null($this->per_page) ? Product::PER_PAGE_DEFAULT : (int)min(100, max(Product::PER_PAGE_DEFAULT, $this->per_page))
        // ]);
    }

    public function rules(): array
    {
        // dd($this->all());
        return [
            'query' => ['nullable', 'string'],
            'category_id' => ['nullable', 'numeric'],
            'subcategory_id' => ['nullable', 'numeric'],
            'user_type' => ['nullable', 'string', 'in:' . User::InfluencerType . ',' . User::BusinessType],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
