<?php

namespace Modules\Campaign\Http\Controllers\API\V2;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\Campaign\Entities\Post;
use Modules\Campaign\Entities\PostImage;
use Modules\Home\Http\Controllers\API\V2\BaseTrait;

class CampaignController extends Controller
{
    use BaseTrait;


    public function createPost(Request $request): JsonResponse
    {
        $user_id = Auth::id();
        $checkUserExist = User::where(['id' => $user_id])->first();
        if (!$checkUserExist)
        {
            return response()->json(['result' => 'Failure', 'message' => 'Account Not Found !', 'status' => 403]);
        }
        if ($checkUserExist->is_deleted == "1")
        {
            http_response_code(400);
            echo json_encode(['result' => 'Failure', 'message' => 'Your account has been deleted by admin.']);
            exit;
        }
        $Validator = Validator::make($request->all(), [
            'media.*' => 'present|file|mimetypes:video/quicktime,video/mp4,video/ogg,image/jpeg,image/jpg,image/png,image/gif|max:500000',
            'post_name' => "required",
            'description' => 'required|max:3000',
            'price_per_post' => 'required',
            'what_to_include' => 'required|max:1500',
            'what_not_to_include' => 'required|max:1500',
        ]);

        if ($Validator->fails())
        {
            return response()
                ->json(['message' => $Validator->errors()
                    ->first()], BAD_REQUEST);
        }

        $input = $request->all();
        $input['post_name'] = request('post_name');
        $input['post_type'] = request('post_type');
        $input['price_per_post'] = request('price_per_post');
        $input['description'] = request('description');
        $input['what_to_include'] = request('what_to_include');
        $input['what_not_to_include'] = request('what_not_to_include');
        $input['hashtag'] = request('hashtag');
        $input['user_id'] = $user_id;
        $input['created_at'] = date('Y-m-d H:i:s');
        $input['updated_at'] = date('Y-m-d H:i:s');
        $insert_post = Post::create($input);
        $post_id = $insert_post->id;

        self::addImagesPost($request, $post_id);
        return response()->json(["message" => "Post created successfully.", 'post_id' => $post_id]);

    }

    private static function addImagesPost(Request $request, $post_id)
    {
        $is_inserted = 0;
        if ($request->hasFile("media"))
        {
            $images = $request->file("media");
            //$thumbnail = $request->file("thumbnail");
            $count = count($images);
            if ($count > 0)
            {
                for ($i = 0; $i < $count; $i++)
                {
                    $image_name = self::uploadImage_control($images[$i], "/app/public/uploads/images/business_images/post_images", 1);

                    $create_array = ["post_id" => $post_id, "media" => $image_name];
                    /* if ($type == 2 && $thumbnail)
                    {
                        $create_array["thumbnail"] = self::uploadImage_control($thumbnail[$i], "/app/public/uploads/images/business_images/post_images", 1);
                    }*/
                    PostImage::create($create_array);
                    $is_inserted++;
                }
            }
        }
        if ($is_inserted > 0)
        {
            response()->json(["message" => "Post image saved successfully."]);
        }
    }

    public static function uploadImage_control($request_profile, string $dir = "/app/public/uploads/images/business", $multiple = false)
    {
        /* user can send single or multiple image in first argument can accept request and image path also*/

        /* all images are saving in storage folder which is root */

        $return_data = "";
        $profile = null;
        if ($multiple)
        {
            $profile = $request_profile;
        }
        else if ($request_profile->hasFile("media"))
        {
            $profile = $request_profile->file("media");
        }

        if ($profile)
        {
            $path = storage_path() . $dir;
            $file_name = $profile->getClientOriginalName();
            $file_name = uniqid() . "_" . $file_name;
            $file_name = str_replace(array(
                " ",
                "(",
                ")"
            ), "", $file_name);
            if (!file_exists($path))
            {
                mkdir(url($dir));
            }

            $profile->move($path, $file_name);
            $return_data = $file_name;
        }

        return $return_data;
    }


}
