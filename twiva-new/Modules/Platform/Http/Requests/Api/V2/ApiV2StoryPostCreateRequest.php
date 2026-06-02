<?php

namespace Modules\Platform\Http\Requests\Api\V2;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Rules\DateDifferenceRule;
use Modules\User\Entities\User;

class ApiV2StoryPostCreateRequest extends FormRequest
{
    public function rules(): array
    {
        // dd($this->user());
        $basic = [
            'product_id' => [
                'required',
                'numeric',
                Rule::when(
                    $this->user()->type == User::InfluencerType,
                    [Rule::exists('influencer_shop_product', 'id')->where('influencer_id', $this->user()->id)]
                ),
                Rule::when(
                    $this->user()->type == User::BusinessType,
                    [Rule::exists('products', 'id')->where('user_id', $this->user()->id)]
                )
            ],
            'platform_ids' => ['required', 'array'],
            'platform_ids.*' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    // dd($attribute, $value, $fail);
                    $exists = $this->user()->UserPlatforms()->where('platform_id', $value)->exists();
                    if (!$exists) {
                        $fail('Please connect the selected platforms first');
                    }
                }
            ],
            // 'images' => [Rule::requiredIf(!request('images_from_product')), 'array'],
            // 'images.*' => ['required', 'image'],
            'caption' => ['required', 'string',
                Rule::when(in_array(Platform::TwitterID, $this->input('platform_ids', [])), 'max:255'), // 3 => X(Twitter)
                Rule::when(in_array(Platform::TikTokID, $this->input('platform_ids', [])), 'max:100'), // 5 => Tiktok
            ],
            // 'images_from_product' => [Rule::requiredIf(!request('image')), 'array'],
            // 'images_from_product.*' => ['required', 'string'],
            'is_schedule' => ['required', 'boolean'],
            'publish_type' => ['nullable'], // , 'string', 'in:all,photo,video', 'json', 'array'
            'tiktok_privacy' => ['nullable', 'in:PUBLIC_TO_EVERYONE,MUTUAL_FOLLOW_FRIENDS,SELF_ONLY'],
            'tiktok_allow_user_to_comment' => ['nullable', 'in:0,1'],
            'tiktok_allow_user_to_duet' => ['nullable', 'in:0,1'],
            'tiktok_allow_user_to_stitch' => ['nullable', 'in:0,1'],
            'tiktok_your_brand' => ['nullable', 'in:0,1'],
            'tiktok_branded_content' => ['nullable', 'in:0,1'],
        ];
        $schedule = [];
        if ($this->is_schedule == 1) {
            $schedule = [
                'repeat_type' => ['required'],
                'repeated_days' => ['nullable'],
                'number_of_time' => ['nullable', 'min:1', 'max:100'],
                'end_after_type' => ['required'],
                'start_date' => ['required', 'date', 'after_or_equal:now'],
                'end_date' => [
                    'nullable',
                    'date',
                    'after:now',
                    new DateDifferenceRule($this->input('start_date'), 15),
                ],
            ];
        }
        $rules = array_merge($basic, $schedule);
        return $rules;
    }
    public function authorize(): bool
    {
        return true;
    }
}
