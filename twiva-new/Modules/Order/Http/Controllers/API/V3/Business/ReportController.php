<?php

namespace Modules\Order\Http\Controllers\API\V3\Business;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderDetails;

class ReportController extends Controller
{
    public function yearlyReport()
    {
        $start_year = (int)now()->format('Y');
        $end_year = (int)now()->subYears(3)->format('Y');
        $chart = Order::where('business_id', auth('api')->id())
            ->select(DB::raw('sum(shipping_amount + order_amount) as total'), DB::raw('YEAR(created_at) year'))
            ->where('order_status', '=', Order::DELIVERED_STATUS)
            ->where(DB::raw('YEAR(created_at)'), '<=', now()->format('Y'))
            ->where(DB::raw('YEAR(created_at)'), '>', now()->subYears(3)->format('Y'))
            ->groupBy('year')
            ->orderBy('year')
            ->get();
        $totalEarnings = $chart->sum('total');
        $chartYears = $chart->pluck('year')->toArray();

        $chart = $chart->toArray();
        foreach (range($start_year, ($end_year)) as $year)
        {
            if (!in_array($year, $chartYears))
            {
                $index = count($chart);
                $chart[$index]['total'] = 0;
                $chart[$index]['year'] = $year;
            }
        }
        return response()->json(['chart' => collect($chart)->sortBy('year')->values(), 'total' => $totalEarnings]);

    }

    public function monthlyReport()
    {
        $orders = OrderDetails::where('business_id', auth()->id())
            ->whereDate('created_at', '<', now())
            ->whereDate('created_at', '>', now()->subYear())
            ->select('business_id', 'amount', 'quantity', 'created_at')
            ->get();
        $sum = 0;
        foreach ($orders as $order)
        {
            $sum += $order->amount + $order->quantity;
        }
        return response()->json(['Monthly Sale' => $sum]);

    }

}
