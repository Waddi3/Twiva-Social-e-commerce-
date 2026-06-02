<?php

namespace Modules\Front\Http\Controllers\ECommerce;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\SubCategories;
use Modules\Products\Repositories\ECommerce\ECommerceCategoryRepository;

class ECommerceController extends Controller
{
    public function __construct(public  ECommerceCategoryRepository $e_commerce_category_repository)
    {
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        // logger('user login', ['auth' => auth('buyer')->user()]);
        $categories = Category::with('subcategories')->latest()->get();
        return view('front::e-commerce.pages.home', [
            'categories' => $categories,
        ]);
    }

    public function shop()
    {
        $selectedCategory = isset(request()->category) ? request()->category : 0;
        $selectedSubCategory = isset(request()->sub_category) ? request()->sub_category : 0;
        $categoryRepository = new ECommerceCategoryRepository();
        $categories = $categoryRepository->getSubCategories(request()->all());

        return view('front::e-commerce.pages.shop', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'selectedSubCategory' => $selectedSubCategory
        ]);
    }

    public function figma()
    {
        return view('front::e-commerce.pages.figma');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('home::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('home::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('home::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
