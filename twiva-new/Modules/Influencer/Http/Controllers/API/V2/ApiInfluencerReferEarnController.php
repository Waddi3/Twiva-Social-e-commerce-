<?php

namespace Modules\Influencer\Http\Controllers\API\V2;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\User\Entities\User;

class ApiInfluencerReferEarnController
{
    public function getReferralPoints()
    {
        if (!auth('api')->check()) {
            return response()->json(['status' => false, 'message' => 'No authorized user'], 401);
        }
        if (auth('api')->user()->type == User::InfluencerType) {
            return response()->json(['status' => true, 'points' => auth('api')->user()->points, 'referral_code' => auth('api')->user()->referral_code], 200);
        } else {
            return response()->json(['status' => false, 'message' => 'No authorized user'], 401);
        }
    }

    public function redeemReferral(Request $request)
    {
        $user = auth('api')->user();
        if ($user && $user->type ==  User::InfluencerType) {
            $data = $request->all();
            $validator = Validator::make($data, [
                'points' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->messages()->first()], 400);
            }

            return response()->json(array('status' => true, 'message' => "Redeem request sent to admin successfully!"), 200, []);
        } else {
            $error_message = array('message' => "Unauthorized", 'status' => false);
            return response()->json($error_message, 401);
        }
    }
}
