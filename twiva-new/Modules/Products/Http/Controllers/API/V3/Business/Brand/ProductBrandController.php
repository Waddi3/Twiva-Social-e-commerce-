<?php

namespace Modules\Products\Http\Controllers\API\V3\Business\Brand;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Influencer\Entities\Product;
use Modules\Products\Transformers\BrandProductBrandIndexCollection;

class ProductBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function suggestions()
    {
        $per_page = request('per_page') ?? 15;
        $brand = request('brand');
        $brands = Product::select('brand')
            ->distinct()
            ->whereNotNull('brand')
            ->orderBy('brand');
        if ($brand) {
            $brands->where('brand', 'like', "%$brand%");
        }
        return response()->json(['data' => BrandProductBrandIndexCollection::collection($brands->paginate($per_page))->resource]);
    }
}
