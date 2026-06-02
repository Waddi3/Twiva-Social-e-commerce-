<?php

namespace Modules\Campaign\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use function auth;

class CreateCampaignRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $whats_app_groups = $this->input('whatsapp_groups', []);
        if ($whats_app_groups) {
            $price_for_whats_app_campaign = config('twiva.campaign.price_for_whats_app_campaign');

            $whatsapp_groups = Arr::map($whats_app_groups, function ($whats_app_group, $key) use ($price_for_whats_app_campaign) {
                $whats_app_group['price'] = $price_for_whats_app_campaign;
                return $whats_app_group;
            });

            $data = [
                'price_per_post' => $price_for_whats_app_campaign,
                'whatsapp_groups' => $whatsapp_groups,
            ];

            $this->merge($data);
        }
    }


    public function rules(): array
    {
        // dd($this->all());
        return [
            'post_name' => ['required'],
            'what_to_include' => ['nullable'],
            'description' => ['required'],
            'other_requirement' => ['nullable'],
            'price_per_post' => ['required', 'numeric'],
            'expiration_date' => ['required', 'date', 'after_or_equal:now'],
            'hashtag' => ['nullable'],
            'media' => ['nullable', 'array'],
            'media.*' => ['required', 'present', 'file', 'mimetypes:video/quicktime,video/mp4,video/ogg,image/jpeg,image/jpg,image/png,image/gif'],
            'influencers' => ['nullable', 'array'],
            'influencers.*.id' => ['required', 'exists:users,id'],
            'influencers.*.price' => ['required', 'numeric', 'min:1'],
            'influencers.*.complementry_item' => ['nullable', 'string'],
            'products' => ['nullable', 'array'],
            'products.*' => ['required', 'exists:products,id'],
            'whatsapp_groups' => ['nullable', 'array'],
            'whatsapp_groups.*.id' => ['required', 'exists:whats_app_groups,id'],
            'whatsapp_groups.*.price' => ['required', 'numeric', 'min:1'],
            'whatsapp_groups.*.complementry_item' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return auth('api')->id();
    }
}
