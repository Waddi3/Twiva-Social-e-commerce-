<?php

namespace Modules\Influencer\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\Influencer\Entities\Categories;
use Modules\Influencer\Entities\Colors;
use Modules\Influencer\Entities\Sizes;
use Modules\Influencer\Entities\SubCategories;

class InfluencerCategoryController extends Controller
{
    public function categoryListing()
    {
        $categories = Categories::where('id', '>', 8)->get();
        return response()->json(['status' => true, 'message' => 'Categories Listing', 'data' => $categories], 200);

    }


    public function categorySubCategoryListing()
    {
        $categories = Categories::with('subcategories')->where('id', '>', 8)->get();
        return response()->json(['status' => true, 'message' => 'Categories And Subcategories Listing', 'data' => $categories], 200);

    }

    public function subCategoryListing(Request $request, $id)
    {
        $categories = SubCategories::where('category_id', $id)->get();
        if (!empty($request->subcategory_id))
        {

            $categories = SubCategories::where('id', $request->subcategory_id)->first();
        }
        return response()->json(['status' => true, 'message' => 'Sub categories Listing', 'data' => $categories], 200);

    }

    public function getCategories()
    {
        try
        {
            $categories = Categories::where('id', '>', 8)->get();
            $success_message = array('message' => "Categories listing", 'categories' => $categories, 'status' => true);
            return response()->json($success_message, 200);
        }
        catch (Exception $e)
        {
            $error_message = array('error' => $e->getMessage(), 'status' => false);
            return response()->json($error_message, 500);
        }
    }

    public function SizeAndColorList(Request $request)
    {


        $sizes = Sizes::all();
        $colours = Colors::all();

        return response()->json(['status' => true, 'message' => 'Size And Color Listing', 'sizes' => $sizes, 'colors' => $colours], 200);

    }
}
