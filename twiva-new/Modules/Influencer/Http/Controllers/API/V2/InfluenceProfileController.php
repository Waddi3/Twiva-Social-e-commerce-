<?php

namespace Modules\Influencer\Http\Controllers\API\V2;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Influencer\Http\Requests\UpdateInfluencerRequest;
use Modules\Influencer\Http\Resources\InfluencerResource;
use Modules\Influencer\Repositories\InfluencerRepository;
use Modules\User\Entities\User;
use function response;

class InfluenceProfileController
{
    public function influencerDetails()
    {
        return response()->json(['data' => new InfluencerResource(auth('api')->user())]);
    }




    public function changePassword(Request $request)
    {
        $data = $request->all();
        try
        {
            $validator = Validator::make($data, [
                'current_password' => 'required',
                'new_password' => 'required|min:8',
                'confirm_password' => 'required|min:8|same:new_password'
            ]);
            if ($validator->fails())
            {
                return response()->json(['status' => false, 'error' => $validator->errors()], 400);
            }
            $user_id = auth('api')->id();
            $user_email = User::where('id', $user_id)->value('email');
            $user = User::select('password')->where(['id' => $user_id])->first();
            if (!empty($user))
            {
                if (Hash::check($request->current_password, $user->password))
                {
                    $updateData = array(
                        'password' => bcrypt($request->confirm_password),
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                    User::where(['id' => $user_id])->update($updateData);
                    return response()->json(['status' => true, 'message' => 'Password Changed successfuly'], 200);
                }
                else
                {
                    return response()->json(['status' => false, 'message' => 'Your old password is not correct'], 400);
                }
            }
            else
            {
                return response()->json(['status' => false, 'message' => 'Invalid User'], 401);
            }
        }
        catch (Exception $e)
        {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function editProfile(UpdateInfluencerRequest $request)
    {
        $data = $request->validated();
        $influencer = auth('api')->user();
        InfluencerRepository::updateInfluencer($influencer, $data);
        return response()->json([
            'status' => true,
            'message' => 'Profile Edited Successfully',
            'profile_image' => $influencer->fresh()->profileImage
        ]);
    }
}
