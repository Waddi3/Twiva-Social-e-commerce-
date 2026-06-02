<?php

namespace Modules\User\Http\Controllers\API\V1;

use Illuminate\Routing\Controller;
use Modules\User\Http\Requests\Api\ApiBadgeListRequest;
use Modules\User\Http\Requests\Api\ApiBadgeUpdateRequest;
use Modules\User\Jobs\NotifyAdminForNewBadgeJob;
use Modules\User\Repository\Api\ApiBadgeRepository;
use Modules\User\Transformers\Api\ApiBadgeListResource;
use Symfony\Component\HttpFoundation\Response;

class ApiBadgeController extends Controller
{

    public function __construct(public ApiBadgeRepository $api_badge_repository) {}

    public function list(ApiBadgeListRequest $request)
    {
        $filters = $request->validated();
        $badges = $this->api_badge_repository->list($filters);
        return response()->json(ApiBadgeListResource::collection($badges)->resource, Response::HTTP_OK);
    }

    public function update(ApiBadgeUpdateRequest $request)
    {
        $data = $request->validated();
        $result = $this->api_badge_repository->update($data);
        if($result){
            NotifyAdminForNewBadgeJob::dispatch();
        }
        return response()->json(['data' => ['result' => $result]], Response::HTTP_OK);
    }
}
