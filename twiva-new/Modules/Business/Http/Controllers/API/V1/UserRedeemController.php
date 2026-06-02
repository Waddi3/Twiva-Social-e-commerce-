<?php

namespace Modules\Business\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Modules\User\Entities\RedeemUser;
use Modules\User\Entities\User;
use Modules\User\Http\Requests\Redeem\UserRedeemReferralRequest;

class UserRedeemController extends Controller
{
    public function redeemReferral(UserRedeemReferralRequest $request)
    {
        $user = auth('api')->user();

        $validated = $request->validated();

        if ($user && $user->type == User::InfluencerType) {
            RedeemUser::create([
                'user_id' => $user->id,
                'points' => $user->points,
            ]);

            $user->update(['points' => 0]);
        }

        return response()->json(['status' => true, 'message' => "Redeem request sent to admin successfully!"]);
    }
}
