<?php

namespace Modules\Products\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\SubCategories;
use Modules\User\Entities\User;

class UpdateProductRequest extends FormRequest {
    private ?Product $product;

    protected function prepareForValidation()
    {
        $this->product = Product::where('id', request('product_id'))
            ->with('eventTicketProduct')
            ->where('user_id', auth('api')->id())
            ->first();

        $this->merge([
            'has_shipping_fees' => request()->has('has_shipping_fees') ? (boolean)$this->has_shipping_fees : 1,
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'product_id'          => ['required', 'exists:products,id'],
            'name'                => ['required', 'string'],
            'sku'                 => ['nullable', 'string'],
            'category'            => ['required', 'exists:categories,id'],
            'sub_category'        => ['required', Rule::in(SubCategories::where('category_id', $this->category)->pluck('id')->toArray())],
            'price'               => ['required', 'numeric', 'min:0'],
            'retail_price'        => ['required', 'numeric', 'min:0', 'gte:price'],
            'stock'               => ['nullable', 'numeric', 'min:0'],
            'discount'            => ['nullable', 'min:0', 'lt:retail_price', 'numeric'],
            'sizes'               => ['nullable', 'array'],
            'sizes.*'             => ['required', 'exists:sizes,id'],
            'colors'              => ['nullable', 'array'],
            'colors.*'            => ['nullable', 'exists:colors,id'],
            'description'         => ['nullable'],
            'specifications'      => ['nullable', 'array'],
            'specifications.*'    => ['nullable', 'required'],
            'main_image'          => ['nullable', 'image'],
            'images.*'            => ['required', 'image'],
            'has_shipping_fees'   => ['required', 'boolean'],
            'shipping_by_partner' => ['boolean'],
            'brand'               => ['nullable', 'string', 'min:1', 'max:255'],
        ];
        if ($this->product->is_event_ticket_product)
        {
            $rules['date'] = ['required', 'date_format:Y-m-d'];
            $rules['start_time'] = ['required', 'date_format:H:i'];
            $rules['end_time'] = ['required', 'date_format:H:i'];
            $rules['location'] = ['required', 'string'];
            unset($rules['category']);
            unset($rules['sub_category']);
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return $this->product->exists();
    }
}
