<?php

namespace Modules\Products\Http\Requests\ECommerce;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

class ECommerceProductIndexRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'sort_direction' => is_null($this->sort_direction) ? 'asc' : (in_array($this->sort_direction, ['asc', 'desc', 'ASC', 'DESC']) ? $this->sort_direction : 'asc'),
            'page' => is_null($this->page) ? 1 : max((int)$this->page, 1),
            'per_page' => is_null($this->per_page) ? Product::PER_PAGE_DEFAULT : (int)min(100, max(Product::PER_PAGE_DEFAULT, $this->per_page)),
        ]);
    }

    public function rules(): array
    {
        // dd($this->all(), 'in:' . User::InfluencerType . ',' . User::BusinessType);
        return [
            'query' => ['nullable', 'string', 'min:0', 'max:100'],

            'user_type' => ['nullable', 'string', 'in:' . User::InfluencerType . ',' . User::BusinessType],

            'category_id' => ['nullable', 'numeric', 'min:1'],
            'subcategory_id' => ['nullable', 'numeric', 'min:1'],

            'deals' => ['nullable', 'string'],

            'sort_by' => ['nullable', 'string', 'min:0', 'max:100', 'in:retail_price,created_at,updated_at'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc,ASC,DESC'],

            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],

            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],

            'page' => ['nullable', 'numeric'],
            'per_page' => ['nullable', 'numeric'],
            'product_type' => ['nullable','in:products,services']

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
