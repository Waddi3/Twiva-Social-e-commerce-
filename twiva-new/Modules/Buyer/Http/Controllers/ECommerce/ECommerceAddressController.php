<?php

namespace Modules\Buyer\Http\Controllers\ECommerce;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Buyer\Http\Requests\Address\CreateRequest;
use Modules\Buyer\Http\Requests\Address\EditRequest;
use Modules\Home\Entities\Country;
use Modules\Order\Entities\ShippingAddress;

class ECommerceAddressController extends Controller
{
    public function index()
    {
        $addresses = Auth::guard('buyer')->user()->addresses;
        $addresses = $addresses->sortBy('created_at', SORT_DESC);
        return view('front::buyer.address.index', ['addresses' => $addresses]);
    }

    public function create($account = null)
    {

        return view('front::buyer.address.create',
            [
                'buyer' => auth('buyer')->user(),
                'countries' => Country::select('id', 'name')->get(),
            ]);

    }

    public function store(CreateRequest $request)
    {
        $data = $request->validated();
        $data['phone_number'] = str_replace('+', '', $data['phone_number']);

        Auth::guard('buyer')->user()->addresses()->create($data);
        return redirect()->route('e-commerce.buyer.address.index', ['account' => request('account')])->with(['message' => 'Address has been saved']);
    }

    public function edit(ShippingAddress $shippingAddress)
    {
        return view('front::buyer.address.edit', [
            'address' => $shippingAddress,
            'countries' => Country::select('id', 'name')->get(),
        ]);

    }

    public function update(EditRequest $request, ShippingAddress $shippingAddress)
    {
        $data = $request->validated();
        $data = array_filter($data, function ($value) {
            return !is_null($value);
        });
        $data['phone_number'] = str_replace('+', '', $data['phone_number']);
        $shippingAddress->update($data);
        return redirect()->route('e-commerce.buyer.address.index')->with(['message' => 'Address has been updated']);
    }

    public function Delete(ShippingAddress $shippingAddress)
    {
        if (in_array($shippingAddress->id, Auth::guard('buyer')->user()->addresses->pluck('id')->toArray())) {
            $shippingAddress->delete();
            return redirect()->back()->with(['message' => 'Address Has Been Deleted']);
        }
        abort(403);
    }


}
