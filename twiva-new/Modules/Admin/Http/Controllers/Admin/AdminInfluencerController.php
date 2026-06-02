<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Http\Requests\AdminCreateInfluencerRequest;
use Modules\Admin\Http\Requests\AdminUpdateInfluencerRequest;
use Modules\Home\Entities\CountryCode;
use Modules\Influencer\Entities\Categories;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Repositories\InfluencerRepository;
use Modules\Notification\Notifications\Influencer\WelcomeToTwivaNotification;
use Modules\Order\Entities\Order;
use Modules\User\Entities\User;
use Modules\User\Repository\UserRepository;
use Staudenmeir\LaravelMigrationViews\Facades\Schema;

class AdminInfluencerController extends Controller
{
    public function index()
    {
        $users_query = User::with('influencerDetails', 'shop', 'shop.influencerProducts', 'influencerStories')
            ->orderBy('id', 'desc')
            ->where('type', User::InfluencerType);

        if (!Schema::hasView('influencer_shop_products_count')) {
            $query = InfluencerShopProducts::/*withTrashed()->*/ with('influencer')
                ->select('influencer_id', DB::raw("count('id') as products_count"))
                ->groupBy('influencer_id');
            Schema::createView('influencer_shop_products_count', $query);
        }
        $users_query->leftJoin('influencer_shop_products_count', 'influencer_id', '=', 'id');

        $users = $users_query->paginate(10);
//        $start = (new Carbon('first day of this month'))->startOfDay();
//        $end = (new Carbon('last day of this month'))->endOfDay();
        $users->each(function ($user) {
            $user->influencerTotalSales = $user->influencerTotalSales;
        });

        return view('admin::influencer.index', ['users' => $users]);
    }

    public function products($id)
    {
        $products = InfluencerShopProducts::with(['productMainImage', 'productReviewsAndRatings', 'productSales', 'productDetails'])
            ->where('influencer_id', $id)->paginate(10);
        $user = User::with('shop')->where('id', $id)->first();
        return view('admin::influencer.products', compact('products', 'user'));
    }

    public function create()
    {
        $categories = Categories::all();
        $countries = CountryCode::all();
        return view('admin::influencer.create', compact('categories', 'countries'));
    }

    public function store(AdminCreateInfluencerRequest $request)
    {
        $data = $request->validated();

        $data['content_type'] = $data['content_type'] ?? null;
        $data['location'] = $data['location'] ?? 'Nairobi';
        $data['is_verified'] = 1;
        $data['is_profile_completed'] = 1;
        $data['email_verified_at'] = now();
        $invitor_referral_code = $data['invitor_referral_code'] ?? null;
        if ($invitor_referral_code) {
            User::where('referral_code', $invitor_referral_code)->firstOrFail();
        }
        unset($data['invitor_referral_code']);
        $influencer = InfluencerRepository::createInfluencer($data);
        UserRepository::createShop($influencer);
        if ($invitor_referral_code) {
            UserRepository::rewardInvitor($invitor_referral_code, $influencer);
        }
        $influencer->notify(new WelcomeToTwivaNotification());
        return redirect()->route('admin.influencers.index')->with('message', 'Influencer Created Successfully');
    }


    public function show($id)
    {
        $interests = [];
        $influencer = User::withTrashed()->with('influencerDetails', 'shop', 'shop.influencerProducts')
            ->where('id', $id)->firstOrFail();
        return view('admin::influencer.show', compact('influencer', 'interests'/*, 'number'*/));
    }

    public function edit($id)
    {
        $influencer = User::with('InfluencerDetails')->findOrFail($id);
        $interests = $influencer->interests->pluck('id')->toArray();
        $countries = CountryCode::all();
        $categories = Categories::all();
        $data = [
            'id' => $id,
            'influencer' => $influencer,
            'categories' => $categories,
            'interests' => $interests,
            'countries' => $countries,
        ];
        return view('admin::influencer.edit', $data);
    }

    public function update(AdminUpdateInfluencerRequest $request, User $influencer)
    {
        $data = $request->validated();

        $data['content_type'] = $data['content_type'] ?? null;
        $data['location'] = $data['location'] ?? 'Nairobi';
        InfluencerRepository::updateInfluencer($influencer, $data);
        return redirect()->route('admin.influencers.index')->with('message', 'Influencer Updated Successfully');
    }


    public function exportCsv(Request $request)
    {
        return Excel::download(new InfluencersExport(), 'influencers.csv');
    }

    public function exportProductCsv(Request $request, $id)
    {
        return Excel::download(new InfluencerProductsExport($id), 'influencerProducts.csv');
    }


    public function productStatus(Request $request)
    {
        $product = InfluencerShopProducts::find($request->product_id);
        $product->status = $request->status;
        $product->save();
        return response()->json(['success' => 'Status change successfully.']);
    }

    public function verify()
    {
        $users = User::with('influencerDetails', 'shop', 'shop.influencerProducts')
            ->orderBy('users.id', 'desc')
            ->where('type', 'Influencer')
            ->where('is_verified', '1')
            ->paginate(10);
        $users->each(function ($user) {
            $user->influencerTotalSales = $user->influencerTotalSales;
        });
        $data = [
            'users' => $users,
        ];
        return view('admin::influencer.verify', $data);
    }


}
