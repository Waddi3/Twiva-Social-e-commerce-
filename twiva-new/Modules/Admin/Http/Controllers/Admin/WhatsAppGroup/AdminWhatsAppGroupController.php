<?php

namespace Modules\Admin\Http\Controllers\Admin\WhatsAppGroup;

use App\Http\Controllers\Controller;
use Modules\Home\Entities\Category;
use Modules\Platform\Entities\ReasonReject;
use Modules\Platform\Entities\WhatsAppGroup;

class AdminWhatsAppGroupController extends Controller
{

    public function index()
    {
        $whats_app_groups_query = WhatsAppGroup::query()
            ->with('user:id,name')
            ->latest();

        $query = request()->get('query');

        if ($query) {
            $whats_app_groups_query->where(function ($where) use ($query) {
                return $where->where('name', 'like', "%$query%")
                    ->orWhere('status', $query)
                    ->orWhere('number_of_people', 'like', "%$query%")
                    ->orWhere('created_at', 'like', "%$query%")
                    ->orWhereHas('user', function ($or_where_has) use ($query) {
                        return $or_where_has->where('name', 'like', "%$query%");
                    })
                    ->orWhere('user_id', $query);
            });
        }
        $whats_app_groups = $whats_app_groups_query->paginate(10);
        if (request()->ajax()) {
            return view('platform::admin.whatsapp-group.data', [
                'whats_app_groups' => $whats_app_groups
            ])->render();
        }
        return view('platform::admin.whatsapp-group.index', ['whats_app_groups' => $whats_app_groups]);
    }

    public function show($id)
    {
        $whats_app_group = WhatsAppGroup::query()
            ->with(['user:id,name', 'city.country:id,name', 'city:id,name,country_id', 'CategoryWhatsAppGroups.categorizable', 'reasonRejections'])
            ->where('id', $id)
            ->first();
        // dd($whats_app_group?->CategoryWhatsAppGroups->pluck('categorizable'));
        $reasons = ReasonReject::query()->orderBy('order')->get();
        return view('platform::admin.whatsapp-group.show', [
            'whats_app_group' => $whats_app_group,
            'reasons' => $reasons,
        ]);
    }

    public function verified()
    {
        $whats_app_group = WhatsAppGroup::query()
            ->where('id', request('whats_app_group_id'))
            ->first();
        if (request('status') == WhatsAppGroup::STATUSES['rejected'] && request('reason_for_rejections')) {
            $whats_app_group->update([
                'status' => request('status'),
            ]);
            $whats_app_group->reasonRejections()->sync(request('reason_for_rejections'));
        } else {
            $whats_app_group->update([
                'status' => request('status'),
            ]);
        }
        return response()->json([], 200);
    }
}
