<?php

namespace Modules\Shop\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditShopRequest extends FormRequest
{
    public function rules(): array
    {
        $allowedRegex = '/<meta\s* name="facebook-domain-verification"\s* content="[a-zA-Z0-9]*"\s*\/>$/';
        return [
            'shop_name' => ['required'],
            'shop_cover_image_path' => ['nullable', 'file', 'image', 'mimes:jpeg,bmp,png,jpg,gif'],
            'shop_logo_path' => ['nullable', 'file', 'image', 'mimes:jpeg,bmp,png,jpg,gif'],
            'shop_description' => ['required'],
            'domain' => ['required', 'regex:/^[a-z][a-z0-9\s-]+[a-z0-9]$/', Rule::unique('shops', 'domain')->ignore(auth()->id(), 'user_id')],
            'facebook_domain_verification_code' => ['nullable', 'string', "regex:$allowedRegex"]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
