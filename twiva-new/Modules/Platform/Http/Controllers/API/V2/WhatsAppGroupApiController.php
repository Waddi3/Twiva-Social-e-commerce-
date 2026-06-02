<?php

namespace Modules\Platform\Http\Controllers\API\V2;

use Illuminate\Routing\Controller;
use Modules\Platform\Entities\WhatsAppCategory;
use Modules\Platform\Entities\WhatsAppGroup;
use Modules\Platform\Http\Requests\Api\WhatsAppGroupStoreRequest;
use Modules\Platform\Http\Resources\V2\WhatsAppGroupPlatformCollection;
use Symfony\Component\HttpFoundation\Response;

class WhatsAppGroupApiController extends Controller
{
    public function index()
    {
        $user = auth('api')->user();
        $whatsapp_groups = $user->whatsappGroups()
            ->with(['user:id,name', 'city.country:id,name', 'city:id,name,country_id', 'CategoryWhatsAppGroups.categorizable', 'reasonRejections'])
            ->latest('id')
            ->paginate();
        return response()->json(['data' => WhatsAppGroupPlatformCollection::collection($whatsapp_groups)]);
    }


    public function store(WhatsAppGroupStoreRequest $request)
    {
        $user = auth('api')->user();
        $validated = $request->validated();
        $whats_app_groups = [];
        foreach ($validated['whats_app_groups'] as $data) {
            // dd(collect($data['category_ids']));
            // $categories = $data['category_ids'];
            $categories = collect($data['category_ids'])->mapWithKeys(function ($category) {
                return [$category => ['categorizable_type' =>  'whats_app_categories']];
            })->toArray();
            // dd($categories);
            unset($data['category_ids']);
            $whats_app_group = $user->whatsappGroups()->create($data);
            $whats_app_group->categories()->sync($categories);
            $whats_app_groups[] = $whats_app_group;
        }
        return response()->json(['ids' => collect($whats_app_groups)->pluck('id')], Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        $delete_status = WhatsAppGroup::query()
            ->where('id', $id)
            ->delete();
        if ($delete_status) {
            return response()->json(['msg' => 'Delete Successfully'], Response::HTTP_OK);
        }
        return response()->json(['msg' => 'Delete Not Successfully'], Response::HTTP_FORBIDDEN);
    }

    public function categories()
    {
        $user = auth('api')->user();
        $whatsapp_categories = WhatsAppCategory::select(['id', 'name'])->get();
        return response()->json(['data' => $whatsapp_categories]);
    }
}
