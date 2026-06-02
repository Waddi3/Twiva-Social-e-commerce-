<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Buyer\Entities\Buyer;
use Modules\Influencer\Entities\Product;
use Modules\Order\Entities\Order;
use Modules\User\Entities\User;
use Modules\User\Repository\UserRepository;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $users = User::select('type', 'created_at')->get();
        $influencers = $this->getPercentage($users, User::InfluencerType);
        $top5FiveInfluencers = UserRepository::topInfluencers();

        $business = $this->getPercentage($users, User::BusinessType);
        $top5FiveBusinesses = UserRepository::topBusiness();

        $products = Product::select('created_at')->get();
        $productsPercent = $this->getProductPercentage($products);

        $buyers = $this->getBuyerPercentage();

        $orders = $this->getOrdersWithPercent();
        $data = [
            'business' => $business,
            'influencers' => $influencers,
            'buyers' => $buyers,
            'top5FiveBusinesses' => $top5FiveBusinesses,
            'top5FiveInfluencers' => $top5FiveInfluencers,
            'products' => $products,
            'productsPercent' => $productsPercent,
            'orders' => $orders,
        ];
        return view('admin::dashboard', $data);
    }

    public function getOrdersWithPercent()
    {
        $orders = Order::select('order_status', 'created_at')->get();
        $current = $orders
            ->whereIn('status', [0, 1, 2])->count();

        $old = $orders
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->count();
        if ($old == 0)
        {
            return ['current' => $current, 'percent' => 100];
        }
        $new = ($current - $old);
        $change = ($new / $old);
        $percent = ($change * 100);
        return ['current' => $current, 'percent' => $percent];

    }


    public function getPercentage($users, $type)
    {
        $current = $users->where('type', $type)->count();
        $old = $users
            ->where('type', $type)
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->count();
        if ($old == 0)
        {
            return ['current' => $current, 'percent' => 100];
        }
        $new = ($current - $old);
        $change = ($new / $old);
        $percent = ($change * 100);
        return ['current' => $current, 'percent' => $percent];
    }

    public function getBuyerPercentage()
    {
        $current = Buyer::count();
        $old = Buyer::where('created_at', '<', Carbon::now()->subDays(30))
            ->count();
        if ($old == 0)
        {
            return ['current' => $current, 'percent' => 100];
        }
        $new = ($current - $old);
        $change = ($new / $old);
        $percent = ($change * 100);
        return ['current' => $current, 'percent' => $percent];
    }

    public function getProductPercentage($products)
    {
        $oldProducts = $products
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->count();
        if ($oldProducts == 0)
        {
            $productsPercent = 100;
        }
        else
        {
            $newProducts = ($products->count() - $oldProducts);
            $productsChange = ($newProducts / $oldProducts);
            $productsPercent = ($productsChange * 100);
        }
        return $productsPercent;

    }
}
