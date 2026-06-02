<?php

namespace Modules\Influencer\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\User;
use function request;

class ApiInfluenceGuestController extends Controller
{
    //guest acccount
    public function guest()
    {
        $guestacc = User::where('guestacc', 1)->get();
        foreach ($guestacc as $list) {
            //order check
            $ordercheck = DB::table('orders')->where('buyer_id', $list->id)->first();
            $address = DB::table('shipping_address')->where('buyer_id', $list->id)->first();
            $cart = DB::table('shopping_cart')->where('buyer_id', $list->id)->first();
            if (empty($ordercheck) && empty($address) && empty($cart)) {
                DB::table('users')->where('created_at', '<=', Carbon::now()->subHours(24))
                    ->where('guestacc', 1)
                    ->where('id', $list->id)
                    ->delete();
                // DB::table('users')->where('created_at', '<=', Carbon::now()->subHours(24))->where('guestacc',1)->delete();
            }
        }
        $guestget = User::where('guestacc', 1)->orderBy('id', 'desc')->first();
        if (empty($guestget)) {
            $number = 1;
        } else {
            $number = $guestget->id + 1;
        }

        // $number=count($guestacc)+1;
        $guestname = 'guest' . $number;
        $guessemail = 'guestaccount' . $number . '@gmail.com';
        $pass = Hash::make($guestname);
        $data = new User();
        $data->name = $guestname;
        $data->email = $guessemail;
        $data->guestacc = 1;
        $data->password = $pass;
        $data->type = 3;
        $data->user_type = 'Buyer';
        $data->is_active = 1;
        $data->status = 1;
        $data->is_verified = 1;
        $data->save();
        if (request()->has('carts')) {
            $this->saveBuyerCards($data->id);
        }
        if (Auth::attempt(['email' => $guessemail, 'password' => $guestname])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;
            // print_r($user);
            return response()->json(['status' => true, 'is_verified' => 1, 'data' => $user, 'needToUpdatePlatform' => 0, 'message' => 'Successfully logged in', 'token' => $token], 200, []);
        }
        // else
        // {
        //    print($data);
        // }
    }
}
