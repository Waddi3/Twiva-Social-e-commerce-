<?php

namespace Modules\Admin\Http\Controllers\Admin\Badges;


use App\Http\Controllers\Controller;
use Modules\Admin\Http\Requests\Badges\AdminResellerRequestedRequest;
use Modules\Admin\Http\Requests\Badges\AdminResellerUpdateStatusRequest;
use Modules\Admin\Repositories\AdminBadgeRepository;
use Symfony\Component\HttpFoundation\Response;

class AdminBadgeController extends Controller
{

    public function __construct(public AdminBadgeRepository $admin_badge_repository) {}

    public function resellerRequested(AdminResellerRequestedRequest $request)
    {
        $filters = $request->validated();
        $badges = $this->admin_badge_repository->resellerRequested($filters);
        $data = [
            'badges' => $badges,
        ];
        if (request()->expectsJson()) {
            return view('admin::badges.data', $data)->render();
        }
        return view('admin::badges.index', $data);
    }

    public function update(AdminResellerUpdateStatusRequest $request)
    {
        $data = $request->validated();
        $result = $this->admin_badge_repository->update($data);
        return redirect()->back();
        if ($result) {
            return response()->json([], Response::HTTP_OK);
        } else {
            return response()->json([], Response::HTTP_BAD_REQUEST);
        }
    }
}
