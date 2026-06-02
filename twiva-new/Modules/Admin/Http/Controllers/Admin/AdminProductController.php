<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Exports\CampaignOrderExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Influencer\Entities\Categories;
use Modules\Influencer\Entities\Colors;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\Sizes;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['productReviewsAndRatings', 'user' => function ($userQuery) {
            $userQuery->withTrashed()->with('businessDetails');
        }])->orderBy('id', 'desc')->paginate(10);

        $categories = Categories::where('id', '>', '430')->get();
        return view('admin::products.index', compact('products', 'categories'));
    }


    public function show($id)
    {
        $product = Product::with('productImages')->where('id', $id)->first();
        return view('admin::products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::with('productImages')->where('id', $id)->first();
        $categories = Categories::all();
        $colors = Colors::all();
        $sizes = Sizes::all();
        return view('products.edit', compact('product', 'categories', 'colors', 'sizes'));
    }


    public function getSizes(Request $request)
    {
        $sizes = [];
        if ($request->ids == null)
        {
            return response()->json(array('sizes' => $sizes), 200);
        }
        foreach ($request->ids as $id)
        {
            $sizes[] = Sizes::select(['id', 'name'])->where('category_id', $id)->get();
        }
        return response()->json(array('sizes' => $sizes), 200);
    }

    public function exportCsv(Request $request)
    {
        return Excel::download(new ProductsExport(), 'products.csv');
    }

    public function downloadCampaignsOrderCsv()
    {
        return Excel::download(new CampaignOrderExport(), 'campaign-order.csv');
    }
}
