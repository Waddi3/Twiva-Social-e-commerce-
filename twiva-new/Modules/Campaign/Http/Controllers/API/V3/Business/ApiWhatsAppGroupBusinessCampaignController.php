<?php

namespace Modules\Campaign\Http\Controllers\API\V3\Business;

use Illuminate\Routing\Controller;
use Modules\Platform\Http\Requests\Api\WhatsAppGroupListRequest;
use Modules\Platform\Http\Resources\V2\WhatsAppGroupPlatformCollection;
use Modules\Platform\Repositories\WhatsAppGroupRepository;
use Symfony\Component\HttpFoundation\Response;

class ApiWhatsAppGroupBusinessCampaignController extends Controller
{

    public function __construct(public WhatsAppGroupRepository $whats_app_group_repository)
    {
    }

    public function listOfWhatsAppGroup(WhatsAppGroupListRequest $request)
    {
        $filter = $request->validated();
        $whatsapp_groups = $this->whats_app_group_repository->listOfWhatsAppGroup($filter);
        return response()->json(['data' => WhatsAppGroupPlatformCollection::collection($whatsapp_groups)], Response::HTTP_OK);
    }

    public function index()
    {
    }

    public function show()
    {
    }

    public function create()
    {
    }

    public function hire()
    {
    }

    public function cancel()
    {
    }

    public function checkWaivePrompt()
    {
    }
}
