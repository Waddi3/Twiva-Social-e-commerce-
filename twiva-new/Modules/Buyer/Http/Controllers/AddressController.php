<?php

namespace Modules\Buyer\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Buyer\Http\Requests\Address\CreateRequest;
use Modules\Buyer\Http\Requests\Address\EditRequest;
use Modules\Home\Entities\Country;
use Modules\Order\Entities\ShippingAddress;

class AddressController extends Controller
{
    public function index($account)
    {
        $addresses = Auth::guard('buyer')->user()->addresses;
        $addresses = $addresses->sortBy('created_at', SORT_DESC);
        return view('buyer::address.index', ['addresses' => $addresses]);
    }

    public function create($account)
    {

        return view('buyer::address.create',
            [
                'buyer' => auth('buyer')->user(),
                'countries' => Country::select('id', 'name')->get(),
            ]);

    }

    public function store($account, CreateRequest $request)
    {
        $data = $request->validated();
        $data['phone_number'] = str_replace('+', '', $data['phone_number']);

        Auth::guard('buyer')->user()->addresses()->create($data);
        return redirect()->route('buyer.address.index', ['account' => request('account')])->with(['message' => 'Address has been saved']);
    }

    public function edit($account, ShippingAddress $shippingAddress)
    {
        return view('buyer::address.edit', [
            'address' => $shippingAddress,
            'countries' => Country::select('id', 'name')->get(),
        ]);

    }

    public function update($account, EditRequest $request, ShippingAddress $shippingAddress)
    {
        $data = $request->validated();
        $data['phone_number'] = str_replace('+', '', $data['phone_number']);
        $shippingAddress->update($data);
        return redirect()->route('buyer.address.index', ['account' => request('account')])->with(['message' => 'Address has been updated']);
    }

    public function Delete($account, ShippingAddress $shippingAddress)
    {
        if (in_array($shippingAddress->id, Auth::guard('buyer')->user()->addresses->pluck('id')->toArray())) {
            $shippingAddress->delete();
            return redirect()->back()->with(['message' => 'Address Has Been Deleted']);
        }
        abort(403);
    }


}
