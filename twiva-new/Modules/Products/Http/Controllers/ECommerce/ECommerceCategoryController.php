<?php

namespace Modules\Products\Http\Controllers\ECommerce;

use App\Http\Controllers\Controller;
use Modules\Products\Http\Requests\ECommerce\ECommerceCategoryRequest;
use Modules\Products\Http\Requests\ECommerce\ECommerceCategorySuggestionRequest;
use Modules\Products\Repositories\ECommerce\ECommerceCategoryRepository;

class ECommerceCategoryController extends Controller
{

    public function __construct(public  ECommerceCategoryRepository $e_commerce_category_repository)
    {
    }

    public function featured()
    {
        $featured_categories = $this->e_commerce_category_repository->featured();
        return response()->json([
            'featured_categories' => $featured_categories
        ]);
    }

    public function suggestions(ECommerceCategorySuggestionRequest $request)
    {
        $filter = $request->validated();
        $categories_suggestions = $this->e_commerce_category_repository->suggestions($filter);
        return response()->json([
            'categories_suggestions' => $categories_suggestions
        ]);
    }

    public function getCategoryWithSubcategory(ECommerceCategoryRequest $request)
    {
        $filter = $request->validated();
        $start_time = now();
        ['categories' => $categories, 'min' => $min_price, 'max' => $max_price] = $this->e_commerce_category_repository->getSubCategories($filter);
        $end_time = now();
        return response()->json([
            'time' => $end_time->diffInMilliseconds($start_time) . " ms",
            'min_price' => (float)$min_price,
            'max_price' => (float)$max_price,
            'categories' => $categories,
        ]);
    }
}
