<?php

namespace Modules\User\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Modules\User\Http\Requests\Api\V2\ApiV2BrandThemeUpdateRequest;
use Modules\User\Repository\Api\V2\ApiV2BrandThemeRepository;
use Modules\User\Transformers\Api\V2\ApiV2BrandThemeResource;
use Symfony\Component\HttpFoundation\Response;

class ApiV2BrandThemeController extends Controller
{
    public function __construct(public ApiV2BrandThemeRepository $api_v2_brand_theme_repository) {}

    public function index()
    {
        $band_theme = $this->api_v2_brand_theme_repository->index();
        if (!$band_theme) {
            return response([], Response::HTTP_NOT_FOUND);
        }
        return response(new ApiV2BrandThemeResource($band_theme), Response::HTTP_OK);
    }

    public function update(ApiV2BrandThemeUpdateRequest $request)
    {
        $data  = $request->validated();
        $result = $this->api_v2_brand_theme_repository->update($data);
        return response(['data' => ['result' => $result]], Response::HTTP_OK);
    }

    public function languages()
    {
        $languages = $this->api_v2_brand_theme_repository->languages();
        return response(['data' => $languages], Response::HTTP_OK);
    }

    public function interests()
    {
        $interests = $this->api_v2_brand_theme_repository->interests();
        return response(['data' => $interests], Response::HTTP_OK);
    }
}
