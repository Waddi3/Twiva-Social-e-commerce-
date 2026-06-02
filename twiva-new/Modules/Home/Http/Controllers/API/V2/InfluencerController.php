<?php

namespace Modules\Home\Http\Controllers\API\V2;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\User\Entities\User;

class InfluencerController
{
    use BaseTrait;

    public function login(Request $request)
    {
        $Validator = Validator::make($request->all(), ["social_id" => "required", "social_type" => "required", "email" => "nullable|email", "device_id" => "required", "device_type" => "required"]);

        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }


        $type = $request->social_type;
        $social_id = $request->social_id;
        if ($type == "F") {
            $input_data["facebook_id"] = $social_id;
        } else if ($type == "I") {
            $input_data["instagram_id"] = $social_id;
        } else {
            $input_data["twitter_id"] = $social_id;
        }
        $checkUserExist = User::where($input_data)
            ->first();

        $email = $request->email;
        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == '1') {
                return response()
                    ->json(["message" => "Email address does not exist."], BAD_REQUEST);
            }

            if (!$email) {
                $update_data = array();
                $update_data["device_id"] = $request->get("device_id");
                $update_data["device_type"] = $request->get("device_type");

                if ($type == "F") {
                    $update_data["facebook_id"] = $social_id;
                } else if ($type == "I") {
                    $update_data["instagram_id"] = $social_id;
                } else {
                    $update_data["twitter_id"] = $social_id;
                }
                $is_update = $this->updateUser($checkUserExist->id, $update_data);
                if ($is_update) {
                    $user = User::with(["Images", "InterestsOld", "SocialDetail", "price", "InfluencerDetail"])->find($checkUserExist->id);
                    $user->token = $user->createToken('*')->accessToken;
                    return response()->json(["message" => "Login successfully.", "data" => $user]);
                } else {
                    return response()->json(["message" => "Unable to proceed your request,please try later."], BAD_REQUEST);
                }
            }


        }


        $query = User::whereEmail($request->email);
        if ($query->exists()) {
            $user_data = $query->first();
            $user_id = $user_data->id;
            if ($user_data->email == $email) {
                $update_data = array();
                $update_data["email"] = $email;
                $update_data["device_id"] = $request->get("device_id");
                $update_data["device_type"] = $request->get("device_type");
                if ($type == "F") {
                    $update_data["facebook_id"] = $social_id;
                } else if ($type == "I") {
                    $update_data["instagram_id"] = $social_id;
                } else {
                    $update_data["twitter_id"] = $social_id;
                }
                $is_update = $this->updateUser($user_id, $update_data);
                if ($is_update) {
                    $user = User::with(["Images", "InterestsOld", "SocialDetail", "price", "InfluencerDetail"])->find($user_id);
                    $user->token = $user->createToken('*')->accessToken;
                    return response()
                        ->json(["message" => "Login successfully.", "data" => $user]);
                } else {
                    return response()->json(["message" => "Unable to proceed your request,please try later."], BAD_REQUEST);
                }
            } else {
                return response()
                    ->json(["message" => "Unable to proceed your request,please try later."], BAD_REQUEST);
            }
        } else {

            if ($request->get("interests") || $request->hasFile("images")) {
                return $this->register_user($request);
            } else {
                return response()->json(["message" => "Please register user."]);
            }
        }
    }

    public function register_user(Request $request)
    {
        $input = $request->all();

        if (array_key_exists("password", $input)) {
            unset($input["password"]);
        }
        $input["type"] = 'Influencer';
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwyz1234567890";
        $code = substr(str_shuffle($str), 0, 6);
        $input["refferal_code"] = $code;
        if ($social_id = $request->get("social_id")) {
            $social_type = $request->get("social_type");
            switch ($social_type) {
                case 'F':
                    $input["facebook_id"] = $social_id;
                    break;
                case 'I':
                    $input["instagram_id"] = $social_id;
                    break;
                default:
                    $input["twitter_id"] = $social_id;
                    break;
            }
        }

        $user = User::create($input);

        if ($user) {
            $this->addImages($request, $user->id);
            $input["user_id"] = $user_id = $user->id;
            if ($request->get("interests")) {
                $this->insertInterest($request->get("interests"), $user->id);
            }
            InfluencerPrice::create($input);

            SocialDetail::create($input);
            $User = User::with(["Images", "InterestsOld", "SocialDetail", "price"])->find($user_id);
            $User->token = $User->createToken('*')->accessToken;
            return response()
                ->json(["message" => "Register successfully.", "data" => $User]);
        } else {
            return response()->json(["message" => "Unable to proceed your request,please try later."], BAD_REQUEST);
        }
    }


    public function normal_signup(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email|unique:users', 'password' => 'required|min:6', 'image' => "image|mimes:jpeg,jpg,png,gif|max:5120", 'name' => "max:255", "email.required" => "Please enter email address", "email.email" => "Please enter a valid email address", "password.required" => "Please enter password",]);

        if ($validator->fails()) {
            return response()
                ->json(["message" => $validator->errors()
                    ->first()], BAD_REQUEST);
        }

        $input = $request->all();
        if ($request->password) {
            $input['password'] = Hash::make($request->password);
        }

        $input["type"] = 'Influencer';
        $input["name"] = $request->name;
        $input["email"] = $request->email;
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwyz1234567890";
        $code = substr(str_shuffle($str), 0, 6);
        $input["refferal_code"] = $code;
        $user = User::create($input);

        if ($user) {
            $this->addImages($request, $user->id);
            $input["user_id"] = $user_id = $user->id;
            if ($request->get("interests")) {
                $this->insertInterest($request->get("interests"), $user->id);
            }
            InfluencerPrice::create($input);

            if ($request->input('facebook_name')) {
                $insertInfluencerSocialDetail = SocialDetail::insert(['user_id' => $user->id, 'facebook_name' => $request->input('facebook_name'), 'facebook_friends' => $request->input('facebook_friends'), 'twitter_name' => $request->input('twitter_name'), 'twitter_friends' => $request->input('twitter_friends'), 'instagram_name' => $request->input('instagram_name'), 'instagram_friends' => $request->input('instagram_friends')]);
            }

            $User = User::with(["Images", "InterestsOld", "price"])->find($user_id);
            $User->token = $User->createToken('*')->accessToken;

            return response()->json(["message" => "Register successfully.", "data" => $User]);
        } else {
            return response()->json(["message" => "Unable to proceed your request,please try later."], BAD_REQUEST);
        }
    }



    public function addImages(Request $request, $user_id)
    {
        $is_inserted = 0;
        if ($request->hasFile("images")) {
            $images = $request->file("images");
            $count = count($images);
            if ($count > 0) {
                for ($i = 0; $i < $count; $i++) {
                    $image_name = $this->uploadImage($images[$i], "/app/public/uploads/images/influencer_images", 1);
                    InfluencerImages::create(["user_id" => $user_id, "image" => $image_name]);
                    $is_inserted++;
                }
            }
        }
        if ($is_inserted > 0) {
            return response()->json(["message" => "Interest image saved successfully."]);
        }
    }

    public function getInterest(Request $request)
    {
        $interest = Interest::orderBy("id", "desc")->get();
        if (count($interest) > 0) {
            return response()->json(["message" => "Interest get successfully.", "data" => $interest]);
        } else {
            return response()->json(["message" => "No interest found."]);
        }
    }

    private function insertInterest(string $interest, int $user_id)
    {
        $interest_arr = explode(",", $interest);
        $count = count($interest_arr);

        if ($count > 0 && (isset($interest_arr[0]) && !empty($interest_arr[0]))) {
            for ($i = 0; $i < $count; $i++) {
                InfluencerInterests::insert(["user_id" => $user_id, "interest_id" => $interest_arr[$i]]);
            }
        }
    }

    public function getProfile($user_id = null)
    {
        if ($user_id) {
            $id = $user_id;
        } else {
            $user_id = Auth::id();
        }
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }

        }
        $user = User::with(["Images", "InterestsOld", "SocialDetail", "price", "InfluencerDetail"])->find($user_id);
        $mpesaPhoneNumber = MpesaPhoneNumber::select('phone_number')->where('user_id', $user_id)
            ->first();
        $user->mpesaPhoneNumber = $mpesaPhoneNumber;

        return response()->json(["message" => "Profile get successfully.", "data" => $user]);
    }

    public function updatePrice(Request $request)
    {
        $user_id = Auth::id();
        $Validator = Validator::make($request->all(), ["plug_price" => "required|integer", "gig_price" => "required|integer"]);

        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }

        }
        $priceUpdate = InfluencerPrice::where("user_id", Auth::id())->update(["plug_price" => $request->get("plug_price"), "gig_price" => $request->get("gig_price")]);
        if ($priceUpdate) {
            return response()->json(["message" => "Price updated successfully."]);
        } else {
            return response()
                ->json(["message" => "Unable to proceed your request,please try later."], BAD_REQUEST);
        }
    }

    public function updateProfile(Request $request)
    {
        //return json_encode(["files "=>$_FILES," post" => $_POST]);
        /*Pending images updates*/
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }

        }
        $Validator = Validator::make($request->all(), ["profile[]" => "image|mimes:jpeg,jpg,png,gif", "name" => "required", "email" => "required|email", "gender" => "required", "dob" => "required", 'about' => 'required|max:1500', 'phone_number' => 'nullable|digits_between:7,15']);

        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }

        $exists = User::where("email", $request->get("email"))
            ->where("id", "!=", $user_id)->exists();
        if ($exists) {
            return response()->json(['message' => "Email address already taken."], BAD_REQUEST);
        }

        if ($request->hasFile("profile")) {
            $updateUser = InfluencerImages::where('user_id', $user_id)->delete();
            $image = $request->file('profile');
            foreach ($image as $i => $images) {
                if (isset($images) && !empty($images)) {
                    $imageName = $this->uploadImage($images, "/app/public/uploads/images/influencer_images", 1);
                    $imageData = new InfluencerImages();
                    $imageData->user_id = $user_id;
                    $imageData->image = $imageName;
                    $imageData->save();
                }
            }
        }

        $updateUserDetail = User::where('id', $user_id)->update(['name' => $request->input('name'), 'phone_number' => $request->input('phone_number')]);
        $getInfluencerDetail = InfluencerDetail::where('user_id', $user_id)->first();
        if (!empty($getInfluencerDetail)) {
            $updateInfluencerDetail = InfluencerDetail::where('user_id', $user_id)->update(['dob' => $request->input('dob'), 'gender' => $request->input('gender'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'about' => $request->input('about')]);
        } else {
            $insertInfluencerDetail = InfluencerDetail::insert(['dob' => $request->input('dob'), 'gender' => $request->input('gender'), 'user_id' => $user_id, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'about' => $request->input('about')]);
        }
        return response()->json(["message" => "Profile updated successfully."]);

    }

    public function getInfulancerNotificationList(Request $request)
    {
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), []);
        if ($Validator->fails()) {
            return response()
                ->json(['message' => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }


        $getNotificationList = Notification::where('user_id', $user_id)->with("otherUser")
            ->orderBy("id", "DESC")
            ->get();

        foreach ($getNotificationList as $notification) {
            if ($notification->type == "post") {
                $postUserStatus = PostUser::where(['post_id' => $notification->post_id, 'user_id' => $notification->user_id])->first();
                if ($postUserStatus) {
                    $notification->new_status = $postUserStatus->status;
                } else {
                    $notification->new_status = 0;
                }
            } else {
                $postUserStatus = GigUser::where(['gig_id' => $notification->post_id, 'user_id' => $notification->user_id])->first();
                if ($postUserStatus) {
                    $notification->new_status = $postUserStatus->status;
                } else {
                    $notification->new_status = 0;
                }
            }
        }
        return response()
            ->json(["message" => "Notification list get successfully.", "data" => $getNotificationList]);
    }

    public function updateSocialDetail(Request $request)
    {
        /*Pending images updates*/
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }

        }
        $Validator = Validator::make($request->all(), [

        ]);

        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }

        $getInfluencersocialDetail = SocialDetail::where('user_id', $user_id)->first();
        if (!empty($getInfluencersocialDetail)) {
            $updateInfluencerSocialDetail = SocialDetail::where('user_id', $user_id)->update(['facebook_name' => $request->input('facebook_name'), 'facebook_friends' => $request->input('facebook_friends'), 'twitter_name' => $request->input('twitter_name'), 'twitter_friends' => $request->input('twitter_friends'), 'instagram_name' => $request->input('instagram_name'), 'instagram_friends' => $request->input('instagram_friends')]);
        } else {
            $insertInfluencerSocialDetail = SocialDetail::insert(['user_id' => $user_id, 'facebook_name' => $request->input('facebook_name'), 'facebook_friends' => $request->input('facebook_friends'), 'twitter_name' => $request->input('twitter_name'), 'twitter_friends' => $request->input('twitter_friends'), 'instagram_name' => $request->input('instagram_name'), 'instagram_friends' => $request->input('instagram_friends')]);
        }
        return response()->json(["message" => "Social profile updated successfully."]);

    }

    public function updateIntersts(Request $request)
    {
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }

        }
        $Validator = Validator::make($request->all(), [
            // "interest_id"    => "required",
        ]);

        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }

        $selected_interests = InfluencerInterests::selectRaw("group_concat(interest_id) as interest_id ")->where('user_id', $user_id)->first()->interest_id;
        $selected = array();
        if ($selected_interests) {
            $selected = explode(",", $selected_interests);
        }
        $interestsId = $request->interest_id;

        if ($interestsId && $selected_interests) {
            $interestsId = explode(',', $interestsId);
            $deletable = array_diff($selected, $interestsId);
            $insertable = array_diff($interestsId, $selected);
        } else if ($interestsId) {
            $deletable = $selected;
            $insertable = explode(",", $interestsId);
        } else {
            $deletable = $selected;
            $insertable = array();
        }
        if ($deletable && !empty($deletable) && count($deletable) > 0) {

            InfluencerInterests::where('user_id', $user_id)->whereIn("interest_id", $deletable)->delete();
        }
        if ($insertable && !empty($insertable) && count($insertable) > 0) {
            foreach ($insertable as $interestsIds) {
                if (isset($interestsIds) && !empty($interestsIds)) {
                    $interestData = new InfluencerInterests();
                    $interestData->user_id = $user_id;
                    $interestData->interest_id = $interestsIds;
                    $interestData->save();
                }
            }
        }

        return response()
            ->json(["message" => "Interests updated successfully."]);
    }

    public function infulancerDetails(Request $request, $infulancer_id)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), [
            // "infulancer_id"    => "required|numeric",
        ]);
        $infulancer_id = $request->infulancer_id;
        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $infulancerDetails = User::with(["Images", "InterestsOld", "SocialDetail", "price", "InfluencerDetail"])->where(['id' => $infulancer_id])->first();
        $sum_rating = InfulancerRating::where('infulencer_id', $infulancer_id)->sum('rating');
        $infulancerCount = InfulancerRating::where('infulencer_id', $infulancer_id)->count();
        if (!empty($sum_rating && $infulancerCount)) {
            $average_rating = $sum_rating / $infulancerCount;
            $infulancerDetails->rating = round($average_rating, 2);
        } else {
            $infulancerDetails->rating = 0;
        }
        return response()
            ->json(["message" => "Influencer Details get successfully.", "data" => $infulancerDetails]);
    }

    function influencerBid(Request $request)
    {
        $rules = [
            "post_id" => "required|numeric|exists:posts,id",
            "price" => "required|numeric"
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $keys = array_keys($validator->getMessageBag()->toArray());
            $response['errors'] = $validator->getMessageBag()->toArray();
            $response['errors_keys'] = $keys;
            return Response()->json($response, 400);
        }
        $user_id = Auth::id(); // 2234 ;
        PostUser::where(['post_id' => $request->post_id, 'user_id' => $user_id, 'is_bid' => '1'])->update([
            "bid_price" => $request->price,
        ]);
        $response['return'] = true;
        $response['message'] = 'Successfully done.';
        return response()->json($response, 200);
    }

    public function pendingPostList(Request $request, $post_name = null)
    {
        $user = Auth::id(); // 2234 ;
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), [

        ]);

        $pendingPostDetails = PostUser::with("Post")->where(['status' => 6, 'user_id' => $user])->orderby("id", "DESC")
            ->get();
        if (!empty($post_name)) {
            $pendingPostDetails = PostUser::with(['Post' => function ($query) use ($post_name) {
                $query->where('post_name', 'like', '%' . $post_name . '%');
            }])->where(['status' => 6, 'user_id' => $user])->orderby("id", "DESC")->get();
        }
        foreach ($pendingPostDetails as $postDetail) {
            $postDetail->getPrice = PostPrice::select('price', 'complementry_item')->where(['post_id' => $postDetail->post_id, 'infulancer_id' => $user])->first();
        }
        return response()
            ->json(["message" => "Pending Post Details get successfully.", "data" => $pendingPostDetails]);
    }


    public function acceptedPostList(Request $request)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), [

        ]);

        $acceptedPostDetails = PostUser::with("Post")->whereIN('status', [1, 4, 3])
            ->where(['user_id' => $user])->orderby("id", "DESC")
            ->get();
        foreach ($acceptedPostDetails as $postDetail) {
            // $postDetail->getPrice = PostPrice::select('price','complementry_item')->where('post_id',$postDetail->post_id)->first();
            $postDetail->getPrice = PostPrice::select('price', 'complementry_item')->where(['post_id' => $postDetail->post_id, 'infulancer_id' => $user])->first();
            $postDetail->getShareData = ShareData::where(['post_id' => $postDetail->post_id, 'infulancer_id' => $user])->first();
        }
        return response()
            ->json(["message" => "Accept Post Details get successfully.", "data" => $acceptedPostDetails,]);
    }

    public function pendingGigList(Request $request, $gig_name = null)
    {
        $user = Auth::id();
        // $user = 2234;
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), []);

        $pendingGigDetails = GigUser::with("Gig")->where(['status' => 5, 'user_id' => $user])->orderby("id", "DESC")
            ->get();
        if (!empty($gig_name)) {
            $pendingGigDetails = GigUser::with(["Gig" => function ($query) use ($gig_name) {
                $query->where('gig_name', 'like', '%' . $gig_name . '%');
            }])->where(['status' => 5, 'user_id' => $user])->orderby("id", "DESC")
                ->get();
        }
        foreach ($pendingGigDetails as $gigDetail) {
            $gigDetail->getPrice = GigPrice::select('price', 'complementry_item')->where(['gig_id' => $gigDetail->gig_id, 'infulancer_id' => $user])->first();
        }
        return response()
            ->json(["message" => "Pending Gig Details get successfully.", "data" => $pendingGigDetails]);
    }

    public function acceptedGigList(Request $request)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), []);

        $acceptedGigDetails = GigUser::with("Gig")->whereIN('status', [1, 3])
            ->where(['user_id' => $user])->orderby("id", "DESC")
            ->get();
        foreach ($acceptedGigDetails as $gigDetail) {
            // $gigDetail->getPrice = GigPrice::select('price','complementry_item')->where('gig_id',$gigDetail->gig_id)->first();
            $gigDetail->getPrice = GigPrice::select('price', 'complementry_item')->where(['gig_id' => $gigDetail->gig_id, 'infulancer_id' => $user])->first();
        }
        return response()
            ->json(["message" => "Accept Gig Details get successfully.", "data" => $acceptedGigDetails]);
    }

    public function AcceptPostRequest(Request $request)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ["post_id" => "required|numeric",]);
        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $post_id = $request->input('post_id');

        // $acceptPostUser = PostUser::where(['post_id' => $post_id, 'user_id' => $user])->update(['status' => 1]);

        $acceptPostUser = PostUser::where(['post_id' => $post_id, 'user_id' => $user])->first();

        $acceptPostPrice = PostPrice::where(['post_id' => $post_id, 'infulancer_id' => $user])->update(['status' => 1]);
        //notification code is pending
        $getInfulancerUserName = User::where(['id' => $user, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getInfulancerUserName->name)) {

            $name = $getInfulancerUserName->name;
        } else {
            $name = "dummy name";
        }
        $offerPriceData = PostPrice::where(['post_id' => $post_id])->first();
        $message = $name . " accepted your post";
        $notificationMessage = "accepted your post";
        $insertNotification = Notification::insert(['user_id' => $offerPriceData->user_id, 'other_user_id' => $user, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'post', 'old_status' => $acceptPostUser->status, 'notify_message' => $notificationMessage, 'post_id' => $post_id]);
        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $post_id
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'accepted', 'id' => $post_id);
        $acceptPostUser->status = 1;
        $acceptPostUser->update();

        $getBusinessUser = User::where(['id' => $offerPriceData->user_id, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, "accepted your post", 'accepted your post', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = "accepted your post", $notifyMessage);
            }
        }
        return response()->json(["message" => "Post accepted successfully."]);

    }

    public function RejectPostRequest(Request $request)
    {

        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ["post_id" => "required|numeric", "reason" => "required",]);
        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $post_id = $request->input('post_id');
        $reason = $request->input('reason');
        $rejectAmount = $request->input('reject_amount');
        // $rejectPostUser = PostUser::where(['post_id' => $post_id, 'user_id' => $user])->update(['status' => 2, 'reason' => $reason,'reject_amount'=>$rejectAmount]);

        $rejectPostUser = PostUser::where(['post_id' => $post_id, 'user_id' => $user])->first();

        // $rejectPostPrice = PostPrice::where(['post_id' => $post_id, 'infulancer_id' => $user])->update(['status' => 2]);
        $offerPriceData = PostPrice::where(['post_id' => $post_id])->first();
        $rejectPostPayment = AdminPayment::where(['post_id' => $post_id, 'infulencer_id' => $user, 'type' => 'post', 'user_id' => $offerPriceData->user_id])->update(['status' => 2]);
        $offerPriceData = PostPrice::where(['post_id' => $post_id])->first();
        $getInfulancerUserName = User::where(['id' => $user, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getInfulancerUserName->name)) {
            $name = $getInfulancerUserName->name;
        } else {
            $name = "dummy name";
        }

        if ($reason == "Low Pay") {
            $message = $name . " negotiated a higher pay";
        }
        $message = $name . " declined your post";
        $notificationMessage = "Post offer price Reject";
        $insertNotification = Notification::insert(['user_id' => $offerPriceData->user_id, 'other_user_id' => $user, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'post', 'old_status' => $rejectPostUser->status, 'notify_message' => $notificationMessage, 'post_id' => $post_id]);
        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $post_id
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'declined', 'id' => $post_id);
        $rejectPostUser->status = 2;
        $rejectPostUser->reason = $reason;
        $rejectPostUser->reject_amount = $rejectAmount;
        $rejectPostUser->update();


        $getBusinessUser = User::where(['id' => $offerPriceData->user_id, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, 'Post offer price Reject', 'declined', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = 'Post offer price Reject', $notifyMessage);
            }
        }
        return response()->json(["message" => "Post rejected successfully."]);

    }

    public function AcceptGigRequest(Request $request)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ["gig_id" => "required|numeric",]);
        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $gig_id = $request->input('gig_id');
        $acceptGigUser = GigUser::where(['gig_id' => $gig_id, 'user_id' => $user])->first();
        //$acceptGigPrice = GigPrice::where(['gig_id' => $gig_id, 'infulancer_id' => $user])->update(['status' => 1]);
        //notification code is start
        $getInfulancerUserName = User::where(['id' => $user, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getInfulancerUserName->name)) {

            $name = $getInfulancerUserName->name;
        } else {
            $name = "dummy name";
        }
        $offerPriceData = GigPrice::where(['gig_id' => $gig_id])->first();
        $message = $name . " accepted your gig";
        $notificationMessage = "accepted your gig";
        $insertNotification = Notification::insert(['user_id' => $offerPriceData->user_id, 'other_user_id' => $user, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'gig', 'old_status' => $acceptGigUser->status, 'notify_message' => $notificationMessage, 'post_id' => $gig_id]);
        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $gig_id
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'gig accepted', 'id' => $gig_id);
        $acceptGigUser->status = 1;
        $acceptGigUser->update();

        $getBusinessUser = User::where(['id' => $offerPriceData->user_id, 'status' => '1', 'is_deleted' => '0'])->first();
        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, "accepted your gig", 'gig accepted', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = "accepted your gig", $notifyMessage);
            }
        }
        return response()->json(["message" => "Gig accepted successfully."]);

    }

    public function RejectGigRequest(Request $request)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ["gig_id" => "required|numeric", "reason" => "required",]);
        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $reason = $request->input('reason');
        $gig_id = $request->input('gig_id');
        $rejectAmount = $request->input('reject_amount');
        // $rejectGigUser = GigUser::where(['gig_id' => $gig_id, 'user_id' => $user])->update(['status' => 2, 'reason' => $reason,'reject_amount'=>$rejectAmount]);

        $rejectGigUser = GigUser::where(['gig_id' => $gig_id, 'user_id' => $user])->first();

        //$rejectGigPrice = GigPrice::where(['gig_id' => $gig_id, 'infulancer_id' => $user])->update(['status' => 2]);

        $offerPriceData = GigPrice::where(['gig_id' => $gig_id])->first();
        $rejectGigPayment = AdminPayment::where(['post_id' => $gig_id, 'infulencer_id' => $user, 'type' => 'gig', 'user_id' => $offerPriceData->user_id])->update(['status' => 2]);

        $getInfulancerUserName = User::where(['id' => $user, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getInfulancerUserName->name)) {

            $name = $getInfulancerUserName->name;
        } else {
            $name = "dummy name";
        }
        if ($reason == "Low Pay") {
            $message = $name . " negotiated a higher pay";
        }
        $message = $name . " declined your gig";
        $notificationMessage = "Gig offer price Reject";
        $insertNotification = Notification::insert(['user_id' => $offerPriceData->user_id, 'other_user_id' => $user, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'gig', 'old_status' => $rejectGigUser->status, 'notify_message' => $notificationMessage, 'post_id' => $gig_id]);
        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $gig_id
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'gig declined', 'id' => $gig_id);
        $rejectGigUser->status = 2;
        $rejectGigUser->reason = $reason;
        $rejectGigUser->reject_amount = $rejectAmount;
        $rejectGigUser->update();

        $getBusinessUser = User::where(['id' => $offerPriceData->user_id, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, 'Gig offer price Reject', 'gig declined', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = 'Gig offer price Reject', $notifyMessage);
            }
        }
        return response()->json(["message" => "Gig rejected successfully."]);

    }

    public function addRating(Request $request)
    {

        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ["rating" => "required|numeric", "comment" => "max:1500", "business_id" => "required", 'type' => 'required', 'post_id' => 'required'


        ]);

        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $getRatingData = Rating::where(['business_id' => $request->input('business_id'), 'type' => $request->input('type'), 'post_id' => $request->input('post_id'), 'user_id' => $user_id])->first();
        if (!empty($getRatingData)) {
            //$rating_status = 2;
            return response()
                // ->json(["message" => "You have already rate this Business."]);
                ->json(["message" => "Rating already submitted."]);
        }
        $insert_rating = new Rating();
        $insert_rating->user_id = $user_id;
        $insert_rating->business_id = $request->input('business_id');
        $insert_rating->rating = $request->input('rating');
        $insert_rating->type = $request->input('type');
        $insert_rating->post_id = $request->input('post_id');
        $insert_rating->comment = $request->input('comment');
        $insert_rating->save();

        return response()
            ->json(["message" => "Rating submitted successfully."]);
    }

    public function infulancerListing(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'limit' => 'numeric | nullable',
            'page' => 'numeric | nullable',
        ]);

        if ($validator->fails()) {
            $ret = array('status' => false, 'message' => $validator->messages()->first());
            return response()->json($ret, 400);
        }

        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                return json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                return json_encode(['message' => 'sucess', 'data' => $data]);
            }
        }

        $limit = $request->limit ? $request->limit : 10;

        $infulancerList = User::with(["Images", "InterestsOld", "SocialDetail", "price", "InfluencerDetail"])
            ->where(['type' => 'Influencer', 'is_deleted' => "0", "status" => "1"]);

        if (!empty($request->name)) {
            $infulancerList = $infulancerList->where('name', 'like', '%' . $request->name . '%');
        }

        $infulancerList = $infulancerList->orderBy('id', 'DESC')->paginate($limit)->toArray();

        return response()->json(array('status' => true, 'data' => $infulancerList['data'], 'current_page' => $infulancerList['current_page'], 'last_page' => $infulancerList['last_page'], 'total_results' => $infulancerList['total'], 'message' => "Influencer listing successfully."), 200, []);
    }

    public function getInfulancerRatingList(Request $request)
    {
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), []);
        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $getInfulancerRatingList = Rating::where('user_id', $user_id)->get();
        return response()
            ->json(["message" => "Influencer rating list successfully.", 'data' => $getInfulancerRatingList]);

    }

    public function createPostDetail(Request $request)
    {
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ['post_id' => 'required', 'post_user_id' => "required", 'text_one' => 'nullable|max:1500', 'text_two' => 'nullable|max:1500', 'text_three' => 'nullable|max:1500'

        ]);

        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $getPostDetail = PostDetail::where(['user_id' => $user_id, 'post_id' => $request
            ->post_id])
            ->first();
        $resend_post_user = PostUser::where(['post_id' => $request
            ->post_id, 'user_id' => $user_id])->first();
        if (!empty($resend_post_user) && $resend_post_user->status == 4) {
            $deletePostMediaDetail = PostDetailsMedia::where(['post_detail_id' => $getPostDetail->id, 'user_id' => $user_id])->delete();
            $deletePostDetail = PostDetail::where(['user_id' => $user_id, 'post_id' => $request
                ->post_id])
                ->delete();
            $update_post_user = PostUser::where(['post_id' => $request
                ->post_id, 'user_id' => $user_id])->update(['status' => 1]);

        }
        $getPostDetail = PostDetail::where(['user_id' => $user_id, 'post_id' => $request
            ->post_id])
            ->first();

        if (!empty($getPostDetail)) {
            return response()->json(["message" => "Your post is already pending for business approval."], BAD_REQUEST);
            /*  $deletePostMediaDetail = PostDetailsMedia::where(['post_detail_id' => $getPostDetail->id, 'user_id' => $user_id])->delete();
              $deletePostDetail = PostDetail::where(['user_id' => $user_id, 'post_id' => $request
                  ->post_id])
                  ->delete();
             $update_post_user = PostUser::where(['post_id'=>$request
              ->post_id,'user_id'=>$user_id])->update(['status'=>"1"]);*/

        }
        $input = $request->all();
        $input['text_one'] = $request->text_one;
        $input['text_two'] = $request->text_two;
        $input['text_three'] = $request->text_three;
        $input['post_id'] = $request->post_id;
        $input['post_user_id'] = $request->post_user_id;
        $input['user_id'] = Auth::id();
        $insert_post = PostDetail::create($input);
        $post_id = $insert_post->id;


        /* $updateGigDetail = GigDetail::where(['user_id' => $user_id, 'gig_id' => $request
           ->gig_id])
           ->update(['time' => $request->time]);*/
        self::createPostImages($request, $post_id);
        $update_post_user = PostUser::where(['post_id' => $request
            ->post_id, 'user_id' => $user_id])->update(['is_submit' => 1]);
        $getBusinessId = Post::where(['id' => $request->post_id])->first();
        $getInfulancerName = User::where(['id' => $user_id, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getInfulancerName->name)) {

            $name = $getInfulancerName->name;
        } else {
            $name = "dummy name";
        }

        $message = "Review " . $name . " Post";
        $notificationMessage = "submitted a post for your approval";
        $insertNotification = Notification::insert(['user_id' => $getBusinessId->user_id, 'other_user_id' => $user_id, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'post', 'old_status' => $resend_post_user->status, 'notify_message' => $notificationMessage, 'post_id' => $request
            ->post_id]);
        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $request
                ->post_id
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'submitted a post for your approval', 'id' => $request
                ->post_id);
        $getBusinessUser = User::where(['id' => $getBusinessId->user_id])
            ->first();
        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, "submitted a post for your approval", 'approval', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = "submitted a post for your approval", $notifyMessage);
            }
        }
        return response()->json(["message" => "Post details submit successfully.", 'post_id' => $post_id]);

    }

    private static function createPostImages(Request $request, $post_id)
    {
        $user_id = Auth::id();

        $is_inserted = 0;
        if ($request->hasFile("media")) {
            $images = $request->file("media");
            $thumbnail = $request->file("thumbnail");
            $count = count($images);
            $date = date("Y-m-d H:i:s");
            if ($count > 0) {
                for ($i = 0; $i < $count; $i++) {
                    // return $user_id;
                    $image_name = self::uploadImage_control($images[$i], "/app/public/uploads/images/business_images/post_detail_image", 1);
                    $create_array = ["post_detail_id" => $post_id, "media" => $image_name, 'user_id' => $user_id, 'created_at' => $date, 'updated_at' => $date,];

                    if ($thumbnail) {
                        $create_array["thumbnail"] = self::uploadImage_control($thumbnail[$i], "/app/public/uploads/images/business_images/post_detail_image", 1);
                    }
                    PostDetailsMedia::insert($create_array);
                    $is_inserted++;
                }
            }
        }
        if ($is_inserted > 0) {
            return response()->json(["message" => "Post image saved successfully."]);
        }
    }

    public static function uploadImage_control($request_profile, string $dir = "/app/public/uploads/images/business", $multiple = false)
    {
        /* user can send single or multiple image in first argument can accept request and image path also*/

        /* all images are saving in storage folder which is root */

        $return_data = "";
        $profile = null;
        if ($multiple) {
            $profile = $request_profile;
        } else if ($request_profile->hasFile("media")) {
            $profile = $request_profile->file("media");
        }

        if ($profile) {
            $path = storage_path() . $dir;
            $file_name = $profile->getClientOriginalName();
            $file_name = uniqid() . "_" . $file_name;
            $file_name = str_replace(array(
                " ",
                "(",
                ")"
            ), "", $file_name);
            if (!file_exists($path)) {
                mkdir(url($dir));
            }

            $profile->move($path, $file_name);
            $return_data = $file_name;
        }

        return $return_data;
    }

    public function CheckInRequest(Request $request)
    {
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ['gig_id' => 'required',]);

        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }
        $date = date('Y-m-d H:i:s');
        $getGigDetail = GigDetail::where(['user_id' => $user_id, 'gig_id' => $request
            ->gig_id])
            ->first();
        if (!empty($getGigDetail)) {
            // new changes//
            /* $deleteGigDetail = GigDetail::where(['user_id' => $user_id, 'gig_id' => $request
                 ->gig_id])
                 ->delete();*/
            return response()->json(["message" => "Your check in request is already pending for business approval."], BAD_REQUEST);

        }
        $StartGig = new GigDetail();
        $StartGig->user_id = $user_id;
        $StartGig->gig_id = $request->gig_id;
        $StartGig->created_at = $date;
        $StartGig->updated_at = $date;
        $StartGig->save();
        $updateCheckInStatus = GigUser::where(['user_id' => $user_id, 'gig_id' => $request
            ->gig_id])
            ->first();
        $getBusinessId = Gig::where(['id' => $request->gig_id])->first();
        $getInfulancerName = User::where(['id' => $user_id, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getInfulancerName->name)) {

            $name = $getInfulancerName->name;
        } else {
            $name = "dummy name";
        }

        $message = "Please Check-in " . $name;
        $notificationMessage = "Please Check-in";
        $insertNotification = Notification::insert(['user_id' => $getBusinessId->user_id, 'other_user_id' => $user_id, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'gig', 'old_status' => $updateCheckInStatus->status, 'notify_message' => $notificationMessage, 'post_id' => $request
            ->gig_id]);
        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $request
                ->gig_id
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'Check-in', 'id' => $request
                ->gig_id);
        $updateCheckInStatus->status = 1;
        $updateCheckInStatus->is_checkin = 1;
        $updateCheckInStatus->update();

        $getBusinessUser = User::where(['id' => $getBusinessId->user_id])->first();
        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, "Please Check-in", 'Check-in', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = "Please Check-in", $notifyMessage);
            }
        }
        return response()
            ->json(["message" => "Gig checkin request sent successfully."]);

    }

    public function StopCheckIn(Request $request)
    {
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ['gig_id' => 'required', 'time' => 'required',]);

        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }

        $updateGigDetail = GigDetail::where(['user_id' => $user_id, 'gig_id' => $request
            ->gig_id])
            ->update(['time' => $request->time]);
        $updateGigUser = GigUser::where(['user_id' => $user_id, 'gig_id' => $request
            ->gig_id])->first();

        $getBusinessId = Gig::where(['id' => $request->gig_id])->first();
        $getInfulancerName = User::where(['id' => $user_id, 'status' => '1', 'is_deleted' => '0'])
            ->first();
        if (!empty($getInfulancerName->name)) {
            $name = $getInfulancerName->name;
        } else {
            $name = "dummy name";
        }

        $message = $name . " checked out ";
        $notificationMessage = "checked out";
        $insertNotification = Notification::insert(['user_id' => $getBusinessId->user_id, 'other_user_id' => $user_id, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'gig', 'old_status' => $updateGigUser->status, 'notify_message' => $notificationMessage, 'post_id' => $request
            ->gig_id]);

        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $request
                ->gig_id
        );


        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'gig checked out', 'id' => $request
                ->gig_id);
        // ->update(['status' => 4, 'updated_at' => date('Y-m-d H:i:s') ]);

        $getBusinessUser = User::where(['id' => $getBusinessId->user_id])
            ->first();
        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, "checked out", 'gig checked out', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = "checked out", $notifyMessage);
            }
        }


        /// gig complete notification to busienss
        $message = "Your Gig has completed successfully";
        $notificationMessage = "Your Gig has completed successfully";
        $insertNotification = Notification::insert(['user_id' => $getBusinessId->user_id, 'other_user_id' => $user_id, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'gig', 'old_status' => $updateGigUser->status, 'notify_message' => $notificationMessage, 'post_id' => $request
            ->gig_id]);
        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $request
                ->gig_id
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'gig completed', 'id' => $request
                ->gig_id);

        $getBusinessUser = User::where(['id' => $getBusinessId->user_id])->first();
        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, "Your gig has completed", 'gig completed', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = "Your Gig has completed successfully", $notifyMessage);
            }
        }
        /// gig complete notification to infulancer
        $message = "Your Gig has completed successfully";
        $insertNotification = Notification::insert(['other_user_id' => $getBusinessId->user_id, 'user_id' => $user_id, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'gig', 'old_status' => $updateGigUser->status, 'notify_message' => $notificationMessage, 'post_id' => $request
            ->gig_id]);
        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $request
                ->gig_id
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'Gig has completed', 'id' => $request
                ->gig_id);
        $updateGigUser->status = 4;
        $updateGigUser->updated_at = date('Y-m-d H:i:s');
        $updateGigUser->update();


        $getInfulencerUser = User::where(['id' => $user_id])
            ->first();
        if (!empty($getInfulencerUser->device_type) && !empty($getInfulencerUser->device_id)) {
            if ($getInfulencerUser->device_type == '2') {
                $this->send_android_notification($getInfulencerUser->device_id, "Your Gig has completed successfully", 'Gig has completed', $message_new);
            } else {
                $this->send_iphone_notification($getInfulencerUser->device_id, $message, $notmessage = "Your Gig has completed successfully", $notifyMessage);
            }
        }
        return response()
            ->json(["message" => "Gig stop successfully."]);

    }

    public function completePostList(Request $request, $post_name = null)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), []);

        $completePostDetails = PostUser::with("Post")->where(['status' => '5', 'user_id' => $user])->orderby("id", "DESC")
            ->get();
        if (!empty($post_name)) {
            $completePostDetails = PostUser::with(['Post' => function ($query) use ($post_name) {
                $query->where('post_name', 'like', '%' . $post_name . '%');
            }])->where(['status' => '5', 'user_id' => $user])->orderby("id", "DESC")
                ->get();
        }
        foreach ($completePostDetails as $postDetail) {
            // $postDetail->getPrice = PostPrice::select('price','complementry_item')->where('post_id',$postDetail->post_id)->first();
            $postDetail->getPrice = PostPrice::select('price', 'complementry_item')->where(['post_id' => $postDetail->post_id, 'infulancer_id' => $user])->first();
            $getPayment = Payment::where(['infulencer_id' => $user, 'type' => 'post', 'post_id' => $postDetail->post_id])->first();

            $postDetail->getShareData = ShareData::where(['post_id' => $postDetail->post_id, 'infulancer_id' => $user])->first();

            if (!empty($getPayment)) {
                $postDetail->getPayment = 2;
            } else {
                $postDetail->getPayment = 3;
            }

            $postDetail->getPrice = PostPrice::select('price', 'complementry_item')->where('post_id', $postDetail->post_id)->first();

            $getRatingData = Rating::where(['user_id' => $user, 'type' => 'post', 'post_id' => $postDetail->post_id])->first();
            if (!empty($getRatingData)) {
                $postDetail->is_rate = 1;
            } else {
                $postDetail->is_rate = 0;
            }

        }
        return response()
            ->json(["message" => "Complete Post Details get successfully.", "data" => $completePostDetails]);

    }

    public function completeGigList(Request $request, $gig_name = null)
    {

        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), []);
        $completeGigDetails = GigUser::with("Gig")->where(['status' => 4, 'user_id' => $user])->orderby("id", "DESC")
            ->get();
        if (!empty($gig_name)) {
            $completeGigDetails = GigUser::with(["Gig" => function ($query) use ($gig_name) {
                $query->where('gig_name', 'like', '%' . $gig_name . '%');
            }])->where(['status' => 4, 'user_id' => $user])->orderby("id", "DESC")
                ->get();
        }

        foreach ($completeGigDetails as $gigDetail) {
            // $gigDetail->getPrice = GigPrice::select('price')->where('gig_id',$gigDetail->gig_id)->first();
            $gigDetail->getPrice = GigPrice::select('price', 'complementry_item')->where(['gig_id' => $gigDetail->gig_id, 'infulancer_id' => $user])->first();
            $getPayment = Payment::where(['type' => 'gig', 'infulencer_id' => $user, 'post_id' => $gigDetail->gig_id])->first();

            if (!empty($getPayment)) {
                $gigDetail->getPayment = 2;
            } else {
                $gigDetail->getPayment = 3;
            }
            // $gigDetail->getPrice = GigPrice::select('price','complementry_item')->where('gig_id',$gigDetail->gig_id)->first();

            $getRatingData = Rating::where(['user_id' => $user, 'type' => 'gig', 'post_id' => $gigDetail->gig_id])->first();
            if (!empty($getRatingData)) {
                $gigDetail->is_rate = 1;
            } else {
                $gigDetail->is_rate = 0;
            }
        }
        return response()
            ->json(["message" => "Complete Gig Details get successfully.", "data" => $completeGigDetails]);

    }

    public function infulancerAccount(Request $request)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = ["totalEarning" => 0, "totalGigCount" => 0, "totalPostCount" => 0, "totalWeeklyPrice" => 0, "totalMonthlyPrice" => 0];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), []);
        $totalGigPrice = DB::table('gig_prices')
            //->selectRaw('sum(price) AS sums')
            ->join('gig_users', 'gig_users.user_id', '=', 'gig_prices.infulancer_id')
            ->whereRaw('(gig_users.gig_id = gig_prices.gig_id)')
            ->where(['gig_prices.infulancer_id' => $user])->where(['gig_prices.status' => 1])
            ->where(['gig_users.user_id' => $user])->where(['gig_users.status' => 4])
            ->sum('price');
        //->sum('price');
        $totalPostPrice = DB::table('post_prices')->join('post_users', 'post_users.user_id', '=', 'post_prices.infulancer_id')
            ->whereRaw('(post_users.post_id = post_prices.post_id)')
            ->where(['post_prices.infulancer_id' => $user])->where(['post_prices.status' => 1])
            ->where(['post_users.user_id' => $user])->where(['post_users.status' => 5])
            ->sum('price');
        $totalEarning = ($totalGigPrice + $totalPostPrice);

        $totalGigCount = GigUser::where(['user_id' => $user, 'status' => 4])->count();

        $totalPostCount = PostUser::where(['user_id' => $user, 'status' => 5])->count();

        //monthly gig data
        $totalMonthlyGigPrice = DB::table('gig_prices')->join('gig_users', 'gig_users.user_id', '=', 'gig_prices.infulancer_id')
            ->whereRaw('(gig_users.gig_id = gig_prices.gig_id)')
            ->where(['gig_prices.infulancer_id' => $user])->where(['gig_prices.status' => 1])
            ->where(['gig_users.user_id' => $user])->where(['gig_users.status' => 4])
            ->whereMonth('gig_users.updated_at', Carbon::now()
                ->month)
            ->sum('price');
        //monthly post data
        $totalMonthlyPostPrice = DB::table('post_prices')->join('post_users', 'post_users.user_id', '=', 'post_prices.infulancer_id')
            ->whereRaw('(post_users.post_id = post_prices.post_id)')
            ->where(['post_prices.infulancer_id' => $user])->where(['post_prices.status' => 1])
            ->where(['post_users.user_id' => $user])->where(['post_users.status' => 5])
            ->whereMonth('post_users.updated_at', Carbon::now()
                ->month)
            ->sum('price');

        $now = Carbon::now()->format('Y-m-d H:i:s');
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $totalMonthlyPrice = ($totalMonthlyPostPrice + $totalMonthlyGigPrice);
        //weekly gig data
        $totalWeeklyGigPrice = DB::table('gig_prices')->join('gig_users', 'gig_users.user_id', '=', 'gig_prices.infulancer_id')
            ->whereRaw('(gig_users.gig_id = gig_prices.gig_id)')
            ->where(['gig_prices.infulancer_id' => $user])->where(['gig_prices.status' => 1])
            ->where(['gig_users.user_id' => $user])->where(['gig_users.status' => 4])
            ->whereBetween('gig_users.updated_at', [$start, $end])->sum('price');
        //weekly post data
        $totalWeeklyPostPrice = DB::table('post_prices')->join('post_users', 'post_users.user_id', '=', 'post_prices.infulancer_id')
            ->whereRaw('(post_users.post_id = post_prices.post_id)')
            ->where(['post_prices.infulancer_id' => $user])->where(['post_prices.status' => 1])
            ->where(['post_users.user_id' => $user])->where(['post_users.status' => 5])
            ->whereBetween('post_users.updated_at', [$start, $end])->sum('price');
        $totalWeeklyPrice = ($totalWeeklyGigPrice + $totalWeeklyPostPrice);
        $data = array('totalEarning' => $totalEarning, 'totalGigCount' => $totalGigCount,
            'totalPostCount' => $totalPostCount, 'totalWeeklyPrice' => $totalWeeklyPrice,
            'totalMonthlyPrice' => $totalMonthlyPrice);
        return response()->json(["message" => "Account Details get successfully.", "data" => $data]);

    }


    public function sharePost(Request $request)
    {
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ['post_id' => 'required']);

        if ($Validator->fails()) {
            return response()->json(["message" => $Validator->errors()->first()], BAD_REQUEST);
        }

        $postId = $request->input('post_id');

        $updatePostUserStatus = PostUser::where(['post_id' => $postId, 'user_id' => $user_id])->first();

        if ($updatePostUserStatus->status == 5) {
            return response()
                ->json(["message" => "Post already shared."], BAD_REQUEST);
        }

        $getBusinessId = Post::where(['id' => $postId])->first();
        $getInfulancerName = User::where(['id' => $user_id, 'status' => '1', 'is_deleted' => '0'])->first();
        if (!empty($getInfulancerName->name)) {
            $name = $getInfulancerName->name;
        } else {
            $name = "dummy name";
        }
        // post share notification
        $message = $name . " promoted you on social media";
        $notificationMessage = "promoted you on social media";

        $insertNotification = Notification::insert(['user_id' => $getBusinessId->user_id, 'other_user_id' => $user_id, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'post', 'old_status' => $updatePostUserStatus->status, 'notify_message' => $notificationMessage, 'post_id' => $postId]);

        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $postId
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'promoted you on social media', 'id' => $postId);
        $getBusinessUser = User::where(['id' => $getBusinessId->user_id])->first();
        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, "promoted you on social media", 'promoted you on social media', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = "Your Post campaign has completed", $notifyMessage);
            }
        }
        // post complete notification to busienss
        $message = "Your Post campaign has completed";
        $notificationMessage = "Your Post campaign has completed";

        $insertNotification = Notification::insert(['user_id' => $getBusinessId->user_id, 'other_user_id' => $user_id, 'message' => $message, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'type' => 'post', 'old_status' => $updatePostUserStatus->status, 'notify_message' => $notificationMessage, 'post_id' => $postId]);

        $notifyMessage = array(
            'sound' => 1,
            'message' => $message,
            'id' => $postId
        );
        $message_new = array('sound' => 1, 'message' => $message,
            'notifykey' => 'Post campaign has completed', 'id' => $postId);
        $getBusinessUser = User::where(['id' => $getBusinessId->user_id])->first();

        if (!empty($getBusinessUser->device_type) && !empty($getBusinessUser->device_id)) {
            if ($getBusinessUser->device_type == '2') {
                $this->send_android_notification($getBusinessUser->device_id, "Your Post campaign has completed", 'Your Post campaign has completed', $message_new);
            } else {
                $this->send_iphone_notification($getBusinessUser->device_id, $message, $notmessage = "Your Post campaign has completed", $notifyMessage);
            }
        }
        /// post complete notification to infulancer
        //$message = "Your Post campaign has completed";
        // $insertNotification = Notification::insert(['other_user_id' => $getBusinessId->user_id, 'user_id' =>$user_id, 'message' => $message, 'created_at' => date('Y-m-d H:i:s') , 'updated_at' => date('Y-m-d H:i:s') ,'type'=>'post','old_status'=>$updatePostUserStatus->status,'notify_message'=>$notificationMessage,'post_id'=>$postId]);
        // $notifyMessage = array(
        //  'sound' => 1,
        //  'message' => $message,
        // 'id' => $postId
        //  );
        //  $message_new = array('sound' =>1,'message'=>$message,
        //  'notifykey'=>'Post campaign has completed','id'=>$postId);
        $updatePostUserStatus->status = 5;
        $updatePostUserStatus->updated_at = date('Y-m-d H:i:s');
        $updatePostUserStatus->update();


        // $getInfulencerUser = User::where(['id' => $user_id])->first();
        // if(!empty($getInfulencerUser->device_type) && !empty($getInfulencerUser->device_id)) {
        // if($getInfulencerUser->device_type == '2') {
        //  $this->send_android_notification($getInfulencerUser->device_id, "Your Post campaign has completed",'Post campaign has completed', $message_new);
        // } else {
        //  $this->send_iphone_notification($getInfulencerUser->device_id, $message, $notmessage ="Your Post campaign has completed", $notifyMessage);
        // }
        //  }
        return response()
            ->json(["message" => "Post has been completed successfully."]);

    }

    public function infulancerGigDetail(Request $request)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ['gig_id' => 'required']);
        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }

        $gigId = $request->input('gig_id');
        $InfluencerGigDetails = GigUser::with("Gig")->where(['gig_id' => $gigId, 'user_id' => $user])->orderby("id", "DESC")->get();

        foreach ($InfluencerGigDetails as $gigDetail) {
            $gigDetail->getPrice = GigPrice::select('price', 'complementry_item')->where(['gig_id' => $gigDetail->gig_id, 'infulancer_id' => $user])->first();

            $getRatingData = Rating::where(['user_id' => $user, 'type' => 'gig', 'post_id' => $gigDetail->gig_id])->first();
            if (!empty($getRatingData)) {
                $gigDetail->is_rate = 1;
            } else {
                $gigDetail->is_rate = 0;
            }
        }

        return response()->json(["message" => "Influencer Gig Details get successfully.", "data" => $InfluencerGigDetails]);
    }

    public function infulancerPostDetail(Request $request)
    {
        $user = Auth::id();
        $checkUserExist = User::where(['id' => $user])->first();

        if (!empty($checkUserExist)) {
            if ($checkUserExist->is_deleted == "1") {
                http_response_code(400);
                echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
                exit;
            }
            if ($checkUserExist->status == "2") {
                $data = [];
                http_response_code(200);
                echo json_encode(['message' => 'sucess', 'data' => $data]);
                exit;
            }
        }
        $Validator = Validator::make($request->all(), ['post_id' => 'required']);
        if ($Validator->fails()) {
            return response()
                ->json(["message" => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }

        $postId = $request->input('post_id');
        $InfluencerPostDetails = PostUser::with("Post")->where(['post_id' => $postId, 'user_id' => $user])->orderby("id", "DESC")->get();
        foreach ($InfluencerPostDetails as $postDetail) {
            // $postDetail->getPrice = PostPrice::select('price','complementry_item')->where(['post_id'=>$postDetail->post_id,''])->first();
            $postDetail->getPrice = PostPrice::select('price', 'complementry_item')->where(['post_id' => $postDetail->post_id, 'infulancer_id' => $user])->first();

            $postDetail->getShareData = ShareData::where(['post_id' => $postDetail->post_id, 'infulancer_id' => $user])->first();

            $getRatingData = Rating::where(['user_id' => $user, 'type' => 'post', 'post_id' => $postDetail->post_id])->first();
            if (!empty($getRatingData)) {
                $postDetail->is_rate = 1;
            } else {
                $postDetail->is_rate = 0;
            }
        }
        return response()->json(["message" => "Influencer Post Details get successfully.", "data" => $InfluencerPostDetails]);
    }

}

