<?php

namespace Modules\Notification\Http\Requests\Translation;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Notification\Entities\NotificationTitle;

class UpdateTranslationRequest extends FormRequest
{
    public function rules(): array
    {
        $validation = ['required', 'string'];
        $rules = [];
        $notifications_list_titles = NotificationTitle::all();
        foreach ($notifications_list_titles as $notifications_list_title) {
            $rules[$notifications_list_title->slug] = $validation;
        }
        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
