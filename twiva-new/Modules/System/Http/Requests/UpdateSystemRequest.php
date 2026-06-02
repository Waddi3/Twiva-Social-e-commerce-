<?php

namespace Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        return $this->merge([
            'shouldSendSMSNotificationsToInfluencersAndBusinesses' => request('shouldSendSMSNotificationsToInfluencersAndBusinesses') == 'on',
            'shouldSendSMSNotificationsToBuyers' => request('shouldSendSMSNotificationsToBuyers') == 'on'
        ]);
    }

    public function rules(): array
    {
        return [
            'shouldSendSMSNotificationsToInfluencersAndBusinesses' => ['required', 'boolean'],
            'shouldSendSMSNotificationsToBuyers' => ['required', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}