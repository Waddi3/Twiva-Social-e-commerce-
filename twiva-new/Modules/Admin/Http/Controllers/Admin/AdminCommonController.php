<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Admin\Events\NewUserRegistered;
use Modules\Business\Repository\BusinessRepository;
use Modules\Buyer\Entities\Buyer;
use Modules\Home\Entities\City;
use Modules\Home\Entities\State;
use Modules\Home\Entities\Transaction;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\Otp;
use Modules\User\Entities\User;
use Modules\User\Repository\UserRepository;
use Staudenmeir\LaravelMigrationViews\Facades\Schema;
use function Psy\debug;

class AdminCommonController extends Controller
{
    public $badRequest = 422;
    public $successStatus = 200;

    public function states(Request $request)
    {
        $states = State::select(['id', 'name'])->where('country_id', $request->country_id)->get();
        return response()->json(array('states' => $states), 200);
    }

    public function cities(Request $request)
    {
        $cities = City::select(['id', 'name'])->where('state_id', $request->state_id)->get();
        return response()->json(array('cities' => $cities), 200);
    }

    public function changePasswordPage(Request $request, $id)
    {
        return view('admin::user.changePassword', ['user' => User::find($id)]);
    }

    public function changePassword(Request $request)
    {
        $password = $request->password;
        $this->validate($request, [
            'password' => 'required|min:8|confirmed',
        ], []);

        if ($password == null || $password == "" && $password < 8) {
            return back()->with('error', 'Please Try again later');
        }

        $user = User::find($request->id);
        $user->password = bcrypt($password);
        $user->save();
        return back()->with('message', 'Password Updated Successfully');
    }


    public function sendCredentials(Request $request)
    {
        $user = User::find($request->id);
        if (!$user) {
            return back()->with('error', 'User Not Found');
        }
        $password = Str::random(10);
        $user->password = bcrypt($password);
        $user->save();
        $test = event(new NewUserRegistered($user, $password));
        return back()->with('message', "Mail Sent SuccessFully");
    }

    public function resetMail(Request $request)
    {
        $reset = new UserController();
        $response = $reset->forgotPasswordOtp($request);
        if ($response->getData()->success) {
            return back()->with('message', "Mail Sent SuccessFully");
        } else {
            return back()->with('error', 'Something Went Wrong, Please Try again Later');
        }
    }

    public function createUser($request)
    {
        $referral_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 8);
        $referral_code = $this->checkReferralCode($referral_code);

        $password = $request->password;
        $otp = mt_rand(99999, 1000000);
        $expiryTime = Carbon::now()->addMinutes(5);

        $data = $request->all();
        $data['logo'] = $data['image'];
        unset($data['busi_name'], $data['comp_email'], $data['busi_number'], $data['building'], $data['pincode'], $data['image']);
        $business = BusinessRepository::createBusiness($data);

        if (!$business) {
            return back()->with('error', 'Something Went Wrong');
        }
        $otp = UserRepository::createOTP($business, Otp::FOR_MOBILE);
        event(new NewUserRegistered($business, $password));
        return $business;
    }


    public function checkReferralCode($referral_code)
    {
        $is_referral_code_exists = User::where('referral_code', $referral_code)->first();
        if ($is_referral_code_exists) {
            $referral_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 8);
            $this->checkReferralCode($referral_code);
        }
        return $referral_code;
    }

    public function fetchData()
    {
        $sort_by = request('sortby') ?? 'id';
        $sort_type = request('sorttype') ?? 'desc';
        $status = request('status');
        $keyword = request('query');
        $type = request('type');
        $eshop_status = request('eshopStatus');
        $keyword = str_replace(" ", "%", $keyword);
        $q = null;
        if ($type == 'influecner_verify') {
            return $this->verifyFilter($sort_type);
        }
        if ($type == User::BusinessType) {
            $view = 'admin::business.data';
            $q = User::withTrashed()->where('type', User::BusinessType)
                ->withCount('influencerShopProducts')
                ->with(['businessDetails', 'products', 'businessTotalSales']);
            // ->orderBy('id', 'desc');
        }

        if ($type == User::InfluencerType) {
            $view = 'admin::influencer.data';
            $q = User::with(['influencerDetails', 'shop', 'shop.orders', 'shop.influencerProducts', 'influencerStories'])
                ->where('type', User::InfluencerType);

            if ($eshop_status == 1 || $eshop_status == 0) {
                $q->whereHas('influencerDetails', function ($que) use ($eshop_status) {
                    $que->where('is_shop_listed', $eshop_status);
                });
            }
            if (!Schema::hasView('influencer_shop_products_count')) {
                $query = InfluencerShopProducts::/*withTrashed()->*/with('influencer')
                    ->select('influencer_id', DB::raw("count('id') as products_count"))
                    ->groupBy('influencer_id');
                Schema::createView('influencer_shop_products_count', $query);
            }
            $q->leftJoin('influencer_shop_products_count', 'influencer_id', '=', 'id');
            if ($sort_by === 'product_count') {
                $q->orderBy('products_count', $sort_type);
            }
        }

        if ($type == Buyer::BuyerType) {
            $view = 'admin::buyer.data';
            $q = Buyer::query();
            // $q->orderBy($sort_by, $sort_type);
        }

        if ($type == User::PartnerType) {
            $view = 'admin::partner.data';
            $q = User::where('type', 'Partner')
                ->orderBy('id', 'desc');
        }

        if (is_null($q)) {
            return view('admin::empty')->render();
        }
        if ($status == 0) {
            $q->onlyTrashed();
        }
        if ($status == 1) {
            $q->withoutTrashed();
        }
        if ($status == 2) {
            $q->withTrashed();
        }

        if (strlen($keyword) >= 1) {
            $q->where(function ($where) use ($keyword) {
                $where->where('id', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', '%' . $keyword . '%')
                    ->orWhere('phone_number', 'like', '%' . $keyword . '%');
            });
        }

        if ($type != Buyer::BuyerType) {
            $q->where('type', $type);
        }


        $q->orderBy($sort_by, $sort_type);
        $users = $q->paginate(10);

        $users->each(function ($user) {
            if ($user->type == User::InfluencerType) {
                $user->influencerTotalSales = $user->influencerTotalSales;
            } elseif ($user->type == User::BusinessType) {
                $user->businessTotalSales = User::calculateBusinessEarning($user);
            }
        });
        return view($view, compact('users'))->render();
    }

    public function basicFilter(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $sort_by = $request->get('sortby');
        $sort_type = $request->get('sorttype');
        $status = $request->get('status');
        $query = $request->get('query');
        $pageName = $request->get('pagename');
        $id = $request->get('id');

        $query = str_replace(" ", "%", $query);
        if ($pageName == 'eshopConnected') {
            $view = 'admin::business.eshopsConnectedData';
            $q = Product::with('company', 'eshopConnected.influencerShop.influencer.influencerDetails')->find($id);
        }


        $q->orderBy($sort_by, $sort_type);


        if ($status == 1 || $status == 0) {
            $q->where('status', $status);
        }
        $product = $q;
        return view($view, compact('product'))->render();
    }


    public function productFilter(Request $request)
    {
        if (!$request->ajax()) {
            return "No Access";
        }

        $sort_by = $request->get('sortby');
        $sort_type = $request->get('sorttype');
        $status = $request->get('status');
        $query = $request->get('query');
        $pageName = $request->get('pagename');
        $rating = $request->get('rating');
        $category = $request->get('category');

        $query = str_replace(" ", "%", $query);
        // if($pageName == 'orders') {
        $view = 'admin::products.data';
        $products = Product::with(['productReviewsAndRatings', 'user' => function ($userQuery) {
            $userQuery->withTrashed()->with('businessDetails');
        }])->orderBy('id', 'desc');
        if (strlen($query) >= 1) {
            $products->where('name', 'like', '%' . $query . '%')
                ->orWhere('id', $query)
                ->orWhere('brand', 'like', "%$query%");
        }
        // }


        // $q->whereHas('businessDetails', function ($que) use ($query) {
        //     $que->where('busi_name', 'like', "%{$query}%");
        // })


        $products->orderBy($sort_by, $sort_type);


        if ($status == 1 || $status == 0) {
            $products->where('status', $status);
        }

        if ($rating != 0) {
            //            $products->where('rating', $rating);
            $products->whereHas('productReviewsAndRatings', function ($query) use ($rating) {
                $query->where('rating', $rating);
            });
        }

        if ($category != 0) {
            $products->where('category', $category);
        }


        $products = $products->paginate(10);

        return view($view, compact('products'))->render();
    }

    public function loginAs($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response([
                'url' => "URL not Found"
            ], 404);
        }
        //        $temp = TempTokens::create([
        //            'user_id' => $id
        //        ]);
        $token = $user->createToken('MyApp')->accessToken;
        //        $temp_id = base64_encode(base64_encode($temp->id));
        $user_id = base64_encode(base64_encode($user->id));
        if (request()->has('login-as-to-campaign') and request('login-as-to-campaign')) {
            $url = config('twiva.bases.app_url') . "#/business/campaigns/" . request('login-as-to-campaign') . "/?type=$user->type&token=$token";
        } else {
            $url = config('twiva.bases.app_url') . "#/?type=$user->type&token=$token";
        }
        //        $url = config('app.business_subdomain_url'). "loginAsAdmin.php?id=". $temp_id."&params=". $temp_user_id;
        return response([
            'url' => $url,
        ], 200);
    }

    public function businessProductSearch(Request $request)
    {
        // return "hi";
        if (!$request->ajax()) {
            return "No Access";
        }

        $query = $request->get('query');
        $id = $request->get('id');

        $q = Product::with(['productReviewsAndRatings', 'eShopConnected'])
            ->where('user_id', $id);
        if (strlen($query) >= 1) {
            $q->where('name', 'like', '%' . $query . '%');
        }
        $products = $q->paginate(10);

        return view('admin::business.productsData', compact('products'))->render();
    }

    public function influencerProductSearch(Request $request)
    {
        // return "hi";
        if (!$request->ajax()) {
            return "No Access";
        }

        $query = $request->get('query');
        $id = $request->get('id');

        $q = InfluencerShopProducts::with(['productMainImage', 'productReviewsAndRatings', 'productSales', 'productDetails'])
            ->where('influencer_id', $id);
        if (strlen($query) >= 1) {
            $q->where('product_title', 'like', '%' . $query . '%');
        }
        $products = $q->paginate(10);

        return view('admin::influencer.productsData', compact('products'))->render();
    }

    public function transactionFilter(Request $request)
    {
        if (!$request->ajax()) {
            return "No Access";
        }
        $pageName = $request->get('pagename');
        $sort_by = $request->get('sortby');
        $sort_type = $request->get('sorttype');
        $query = $request->get('query');
        $transaction_type = $request->get('transaction_type');
        $transaction_status = $request->get('transaction_status');
        $view = 'transactions.data';
        $q = Transaction::with('user');
        $q->orderBy($sort_by, $sort_type);
        if (strlen($query) >= 1) {
            $q->whereHas('user', function ($que) use ($query) {
                $que->where('name', 'like', '%' . $query . '%');
            });
        }
        if (!empty($transaction_type)) {
            $q->where('request_type', $transaction_type);
        }
        if (!is_null($transaction_status)) {
            $q->where('status', $transaction_status);
        }
        $transactions = $q->paginate(10);

        return view($view, compact('transactions'))->render();
    }

    private function verifyFilter($sort_type = 'desc')
    {
        $users = User::with('influencerDetails', 'shop', 'shop.influencerProducts')
            ->orderBy('users.id', $sort_type)
            ->where('type', 'Influencer')
            ->where('is_verified', '1');
        $keyword = \request('query');
        if (Str::length($keyword)) {
            $users->where('name', 'like', "%$keyword%")
                ->orWhere('email', 'like', "%$keyword%")
                ->orWhere('id', $keyword);
        }
        $users = $users->paginate(10);
        $users->each(function ($user) {
            $user->influencerTotalSales = collect();
        });
        return view('admin::influencer.verifydata', ['users' => $users])->render();
    }
}
