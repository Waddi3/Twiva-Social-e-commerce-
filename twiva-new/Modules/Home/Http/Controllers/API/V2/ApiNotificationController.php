<?php

namespace Modules\Home\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Notification\Entities\Notification;

class ApiNotificationController extends Controller
{
    public function getNotificationListing(Request $request)
    {

        $user = auth('api')->user();
        if ($user) {
            try {
                $input = $request->all();
                $validator = Validator::make($input, [
                    'limit' => 'numeric | nullable',
                    'page' => 'numeric | nullable',
                ]);

                if ($validator->fails()) {
                    $ret = array('success' => 0, 'message' => $validator->messages()->first());
                    return response()->json($ret);
                }
                $limit = $request->limit ? $request->limit : 10;
                $notifications = Notification::where('user_id', $user->id)->orderBy('id', 'desc')->paginate($limit)->toArray();

                return response()->json(array('success' => true, 'data' => $notifications['data'], 'current_page' => $notifications['current_page'], 'last_page' => $notifications['last_page'], 'total_results' => $notifications['total'], 'message' => "Notification Listing"), 200, []);
            } catch (Exception $e) {
                $error_message = array('error' => $e->getMessage(), 'status' => false);
                return response()->json($error_message, 500);
            }
        } else {
            $error_message = array('message' => "Unauthorized", 'status' => false);
            return response()->json($error_message, 401);
        }
    }

    public function markNotification(Request $request)
    {
        $user = auth('api')->user();
        if ($user) {
            try {
                $data = $request->all();
                $validator = Validator::make($data, [
                    'notification_id' => 'required | exists:notifications,id'
                ]);
                if ($validator->fails()) {
                    return response()->json(['status' => false, 'error' => $validator->messages()->first()], 400);
                }
                $notification = Notification::findOrFail($data['notification_id']);
                $notification->is_read = 1;
                $notification->save();
                $success_message = array('status' => true, 'message' => "Notification marked read successfully", "data" => $notification);
                return response()->json($success_message, 200);
            } catch (Exception $e) {
                $error_message = array('error' => $e->getMessage(), 'status' => false);
                return response()->json($error_message, 500);
            }
        } else {
            $error_message = array('status' => false, 'message' => "Unauthorized");
            return response()->json($error_message, 401);
        }
    }

    public function getUnreadCount()
    {

        $user = auth('api')->user();
        if ($user) {
            try {
                $count = Notification::where('user_id', $user->id)->where('is_read', 0)->count();
                $success_message = array('status' => true, 'message' => "Unread Count fetched", 'unread_count' => $count);
                return response()->json($success_message, 200);
            } catch (Exception $e) {
                $error_message = array('error' => $e->getMessage(), 'status' => false);
                return response()->json($error_message, 500);
            }
        } else {
            $error_message = array('message' => "Unauthorized", 'status' => false);
            return response()->json($error_message, 401);
        }
    }
}
