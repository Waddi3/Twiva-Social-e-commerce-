<?php

namespace Modules\Buyer\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Http\Requests\Account\BuyerAccountUpdateRequest;

class BuyerAccountController extends Controller
{
    public function account($account)
    {
        $buyer = auth('buyer')->user();
        return view('buyer::account.edit', [
            'buyer' => $buyer,
        ]);
    }

    public function update($account, BuyerAccountUpdateRequest $request)
    {
        $validated = $request->validated();

        if (array_key_exists('rep_mobile', $validated) and $validated['rep_mobile']) {
            $validated['rep_phone_code'] = str_replace('+', '', explode('-', $validated['rep_mobile'])[0]);
            $validated['rep_phone_number'] = explode('-', $validated['rep_mobile'])[1];
            if ($validated['rep_phone_number'] === '') {
                unset($validated['rep_phone_code']);
                unset($validated['rep_phone_number']);
            }
        }
        unset($validated['rep_mobile']);

        $buyer = auth('buyer')->user();

        if ($validated['class'] == Buyer::INDIVIDUAL) {
            $validated['rep_name'] = $buyer->name;
            $validated['retail_store_name'] = null;
            $validated['rep_phone_code'] = $buyer->phone_code;
            $validated['rep_phone_number'] = $buyer->phone_number;
        }
        $buyer->update($validated);
        return redirect()->route('buyer.account.index', ['account' => request('account')])->with(['message' => 'Account info has been updated']);
    }
}
