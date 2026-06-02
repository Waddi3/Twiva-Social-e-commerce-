<?php

namespace Modules\Campaign\Http\Controllers\Admin;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Admin\Http\Requests\AdminUpdateCampaignApproveStatusRequest;
use Modules\Campaign\Entities\Post;

class AdminCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return string
     */
    public function index()
    {
        $per_page = request('per_page') ?? 15;
        $campaigns = Post::with([
            //            'Images',
            //            'postDetail',
            'user:id,name,email,phone_number,phone_code,status,profile_image',
            //            'price',
            //            'postUserComplete'
        ])
            ->withCount('influencersInPost')
            ->latest();
        $campaigns = $this->filterCampaigns($campaigns);
        $data = [
            'campaigns' => $campaigns->paginate($per_page),
        ];
        if (request()->expectsJson()) {
            return view('campaign::admin.campaigns.data', $data)->render();
        }
        return view('campaign::admin.campaigns.index', $data);
    }

    public function changeApproveStatus(AdminUpdateCampaignApproveStatusRequest $request)
    {
        $campaign = Post::find($request->campaign_id);
        if (!$campaign) {
            return response()->json([
                'status' => false,
                'message' => 'The campaign not found'
            ]);
        }
        try {
            $campaign->approve_status = $request->status;
            $campaign->save();
            return response()->json([
                'status' => true,
                'message' => 'The campaign status has updated'
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ]);
        }
    }

    private function filterCampaigns(Builder $campaigns)
    {
        if (request()->has('query') and Str::length(request('query')) > 0) {
            $campaigns->where(function ($where) {
                $where->where('post_name', 'like', '%' . request('query') . '%')
                    ->orWhere('user_type', 'like', '%' . request('query') . '%')
                    ->orWhere('id', request('query'));
            });
        }
        if (request()->has('type')) {
            if (request('type') == 'INVITATION') {
                $campaigns->whereNull('post_type');
            }
            if (request('type') == 'BROADCAST') {
                $campaigns->where('post_type', 'BROADCAST');
            }
        }
        return $campaigns;
    }
}
