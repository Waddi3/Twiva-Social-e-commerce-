<?php

namespace Modules\Chat\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Modules\Chat\Http\Repositories\Api\V1\ApiChatSettingsRepository;
use Modules\Chat\Http\Requests\Api\V1\ApiChatSettingUpdateRequest;
use Symfony\Component\HttpFoundation\Response;

class ApiChatSettingsController extends Controller
{

    public function __construct(public ApiChatSettingsRepository $api_chat_settings_repository) {}

    public function settings()
    {
        $settings = $this->api_chat_settings_repository->settings();
        return response()->json(['data' => $settings], Response::HTTP_OK);
    }

    public function update(ApiChatSettingUpdateRequest $request)
    {
        $data = $request->validated();
        $data = Arr::map($data, function ($value, $key) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        });
        $result = $this->api_chat_settings_repository->update($data);
        return response()->json(['data' => $result], Response::HTTP_OK);
    }
}
