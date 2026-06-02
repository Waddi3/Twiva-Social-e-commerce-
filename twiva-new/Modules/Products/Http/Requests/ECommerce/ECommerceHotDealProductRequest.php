<?php

namespace Modules\Products\Http\Requests\ECommerce;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

class ECommerceHotDealProductRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'page' => is_null($this->page) ? 1 : max((int)$this->page, 1),
            'per_page' => is_null($this->per_page) ? Product::HOT_DEAL_PER_PAGE_DEFAULT : (int)min(100, max(Product::HOT_DEAL_PER_PAGE_DEFAULT, $this->per_page))
        ]);
    }

    public function rules(): array
    {
        return [
            'user_type' => ['nullable', 'string', 'in:' . User::InfluencerType . ',' . User::BusinessType],

            'page' => ['nullable', 'numeric'],
            'per_page' => ['nullable', 'numeric'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
