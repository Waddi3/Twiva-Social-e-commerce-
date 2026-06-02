<?php

namespace Modules\Platform\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateStoryPostRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [];
        if (request('is_schedule') == 1)
        {
            $rules = [
                'product_id' => 'required',
                'platform' => 'required',
                'image' => [Rule::requiredIf(!request('images_from_product')), 'array'],
                'image.*' => 'required|image',
                'caption' => 'required',
                'start_date' => ['required', 'date','after_or_equal:now'],
                'repeat_type' => 'required',
                'repeated_days' => 'nullable',
                'end_date' => ['nullable','date','after:now'],
                'number_of_time' => 'nullable',
                'end_after_type' => 'required',
                'images_from_product' => [Rule::requiredIf(!request('image')), 'array'],
                'images_from_product.*' => ['required', 'string'],
                'is_schedule' => ['required']
            ];
        }
        else
        {
            $rules = [
                'product_id' => 'required',
                'platform' => 'required',
                'image' => [Rule::requiredIf(!request('images_from_product')), ' array'],
                'image.*' => 'required|image',
                'caption' => 'required',
                'images_from_product' => [Rule::requiredIf(!request('image')), 'array'],
                'images_from_product.*' => ['required', 'string'],
                'is_schedule' => ['required']
            ];
        }
        return $rules;
    }

}