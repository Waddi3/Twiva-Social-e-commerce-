<?php

namespace Modules\Coupon\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Coupon\Entities\Coupon;
use Modules\Coupon\Http\Requests\Admin\AdminCreateCouponRequest;
use Modules\Coupon\Http\Requests\Admin\AdminEditCouponRequest;

class AdminCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $coupons_query = Coupon::query()->latest()->forAdmin()->withCount('couponBuyers as all_use');
        $query = request()->get('query');

        if ($query) {
            $coupons_query->where(function ($where) use ($query) {
                return $where->where('name', 'like', "%$query%")
                    ->orWhere('code', 'like', "%$query%")
                    ->orWhere('amount', $query)
                    ->orWhere('max_usage', $query)
                    ->orWhere('type', $query);
            });
        }
        $coupons = $coupons_query->paginate(10); // ->withQueryString();
        if (request()->ajax()) {

            return view('coupon::admin.data', [
                'coupons' => $coupons
            ])->render();
        }
        return view('coupon::admin.index', [
            'coupons' => $coupons
        ]);
    }

    public function activation()
    {
        $activation = request('active');
        $coupon_id = request('coupon_id');
        $coupon = Coupon::where('id', $coupon_id)->update([
            'is_active' => $activation,
        ]);
        return response()->json(['active' => $activation], 200);
    }

    public function toggleBypassTheMinimum()
    {
        $status = request('status');
        $coupon_id = request('coupon_id');
        $coupon = Coupon::where('id', $coupon_id)->update([
            'bypass_the_minimum' => $status,
        ]);
        return response()->json(['status' => $status], 200);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $code = (new Coupon())->generateUniqueCode();
        return view('coupon::admin.create', [
            'code' => $code,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(AdminCreateCouponRequest $request)
    {
        $data = $request->validated();
        Coupon::create($data);
        return redirect()->route('admin.coupons.index');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('coupon::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Coupon $coupon)
    {

        return view('coupon::admin.edit', [
            'coupon' => $coupon
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(AdminEditCouponRequest $request, Coupon $coupon)
    {
        $data = $request->validated();
        $coupon->update($data);
        return redirect()->route('admin.coupons.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Coupon $coupon)
    {
        if ($coupon->delete()) {
            $alert = ['message' => 'Deleted Successfully'];
        } else {
            $alert = ['error' => 'Please try again'];
        }
        return redirect()->route('admin.coupons.index')->with($alert);
    }
}
