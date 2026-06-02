<?php

namespace Modules\Admin\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Modules\Home\Entities\Category;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\SubCategories;

class AdminCategoryController extends Controller
{

    public function index()
    {
        $categories = Category::where('id', '>', 8)->paginate(100);
        return view('admin::categories.index', ['categories' => $categories]);
    }

    public function activation()
    {
        $result = Category::where('id', request('category_id'))->update([
            'is_active' => request()->get('status'),
        ]);
        return response(['message' => $result ? 'Updated!' : 'Not Updated!']);
    }

    public function featured()
    {
        $result = Category::where('id', request('category_id'))->update([
            'is_featured' => request()->get('status'),
        ]);
        return response(['message' => $result ? 'Featured!' : 'Not Featured!']);
    }

    public function editPage(int $id) {
        $category = Category::find($id);
        if( $category ) {
            return view('admin::categories.edit', [
                'category' => $category
            ]);
        }
        return redirect('admin.category.index')->withErrors([
            'category' => 'Not found'
        ]);
    }

    public function createPage() {
        return view('admin::categories.create');
    }

    public function create(Request $request) {

        $isActive = $request->has('is_active') && $request->input('is_active') == 'on' ? true : false;
        $isFeatured = $request->has('is_featured') && $request->input('is_featured') == 'on' ? true : false;

        $category = Category::create([
            'name' => $request->input('name'),
            'is_active' => $isActive,
            'is_featured' => $isFeatured
        ]);

        if( !$category ) {
            return redirect()->route('admin.category.create')->withErrors([
                'category' => 'A problem occurred while creating Category'
            ]);
        }

        return redirect()->route('admin.category.index')->with('message', 'Category created successfully');
    }

    public function update(Request $request) {
        $category = Category::find($request->id);

        if( !$category ) {
            return redirect()->route('admin.category.index');
        }

        $isActive = $request->has('is_active') && $request->input('is_active') == 'on' ? true : false;
        $isFeatured = $request->has('is_featured') && $request->input('is_featured') == 'on' ? true : false;

        $category->name = $request->name;
        $category->is_active = $isActive;
        $category->is_featured = $isFeatured;
        $category->save();

        return redirect()->route('admin.category.edit', ['id' => $category->id])->with('message', 'Category updated successfully');
    }

    // --- Sub Categories ---
    public function subCreatePage() {
        $categories = Category::select('id', 'name')->get();
        return view('admin::categories.subcategory-create', [
            'categories' => $categories
        ]);
    }

    public function subCreate(Request $request) {
        $names = explode("\r\n", $request->name);
        $isActive = $request->has('is_active') && $request->input('is_active') == 'on' ? true : false;

        try {
            foreach( $names as $name ) {
                $category = SubCategories::create([
                    'name' => $name,
                    'category_id' => $request->category,
                    'is_active' => $isActive,
                ]);
            }

            return redirect()->route('admin.subcategory.index')->with('message', 'Sub-Categories created successfully');
        }
        catch( Exception $exception ) {
            return redirect()->route('admin.subcategory.create')->withErrors([
                'subcategory' => 'A problem occurred while creating Sub-Category'
            ]);
        }

    }

    public function subActivation()
    {
        $result = SubCategories::where('id', request('subcategory_id'))->update([
            'is_active' => request()->get('status'),
        ]);
        return response(['message' => $result ? 'Updated!' : 'Not Updated!']);
    }

    public function subCategories() {
        $subCategories = SubCategories::paginate(100);
        return view('admin::categories.subcategory-index', ['subcategories' => $subCategories]);
    }

    public function subEditPage(int $id) {
        $subcategory = SubCategories::find($id);
        if( $subcategory ) {
            $categories = Category::select('id', 'name')->get();
            return view('admin::categories.subcategory-edit', [
                'subcategory' => $subcategory,
                'categories' => $categories

            ]);
        }
        return redirect('admin.subcategory.index')->withErrors([
            'category' => 'Not found'
        ]);
    }

    public function subUpdate(Request $request) {
        $subCategory = SubCategories::find($request->id);

        if( !$subCategory ) {
            return redirect()->route('admin.category.subcategory-index');
        }

        // Update categories of
        if($subCategory->category_id != $request->category) {
            Product::where('sub_category', $subCategory->id)->update(['category' => $request->category]);
        }

        $isActive = $request->has('is_active') && $request->input('is_active') == 'on' ? 1 : 0;

        $subCategory->name = $request->name;
        $subCategory->is_active = $isActive;
        $subCategory->category_id = $request->category;
        $subCategory->save();

        return redirect()->route('admin.subcategory.edit', ['id' => $subCategory->id])->with('message', 'Sub Category updated successfully');
    }
}
