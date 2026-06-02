<?php

namespace Modules\Products\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Influencer\Entities\SubCategories;
use Modules\User\Entities\User;

class CreateProductRequest extends FormRequest {
    protected function prepareForValidation()
    {
        $this->merge([
            'has_shipping_fees' => request()->has('has_shipping_fees') ? (boolean)$this->has_shipping_fees : 1,
            'shipping_by'       => request()->has('shipping_by_partner') ? (boolean)$this->shipping_by_partner : 0,
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'name'             => ['required', 'string'],
            'sku'              => ['nullable', 'string'],
            'category'         => ['required', 'exists:categories,id'],
            'sub_category'     => ['required', Rule::in(SubCategories::where('category_id', $this->category)->pluck('id')->toArray())],
            'price'            => ['required', 'numeric', 'min:0'],
            'retail_price'     => ['required', 'numeric', 'min:0', 'gte:price'],
            'stock'            => ['nullable', 'numeric', 'min:0'],
            'discount'         => ['nullable', 'min:0', 'lt:retail_price', 'numeric'],
            'sizes'            => ['nullable', 'array'],
            'sizes.*'          => ['required', 'exists:sizes,id'],
            'colors'           => ['nullable', 'array'],
            'colors.*'         => ['nullable', 'exists:colors,id'],
            'description'      => ['nullable'],
            'specifications'   => ['nullable', 'array'],
            'specifications.*' => ['nullable', 'required'],
            'main_image'       => ['required', 'image'],
            'images.*'         => ['required', 'image'],
            'brand'            => ['nullable', 'string', 'min:1', 'max:255'],
        ];
        $isEventProduct = SubCategories::where('id', request('sub_category'))
            ->where('name', SubCategories::EventSubCategoryName)->exists();
        if($isEventProduct){
            $rules['date'] = ['required','date_format:Y-m-d'];
            $rules['start_time'] = ['required','date_format:H:i'];
            $rules['end_time'] = ['required','date_format:H:i'];
            $rules['location'] = ['required','string'];
        }
        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
