<?php

namespace Modules\Buyer\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Support\Str;
use Modules\Buyer\Entities\Buyer;
use Modules\Buyer\Http\Requests\Api\V1\ApiCustomerListRequest;
use Modules\Buyer\Http\Requests\Api\V1\ApiCustomerUpdateRequest;
use Modules\Buyer\Http\Resources\Api\ApiBuyerResource;
use Modules\Buyer\Http\Resources\Api\ApiBuyerShowResource;
use Symfony\Component\HttpFoundation\Response;

class ApiCustomerController
{
    public function customerList()
    {
        $user = auth('api')->user();
        $buyers = $user
            ->buyers()
            ->select([
                'id',
                'name',
                'phone_code',
                'phone_number',
                'created_at',
                'is_verified',
            ]);
        return response()->json(['data' => ['buyers' => ApiBuyerResource::collection($buyers->get()->makeHidden('password'))->resource]]);
    }

    public function show($id)
    {
        $user = auth('api')->user();
        $buyer = $user
            ->buyers()
            ->with(['addresses' => function ($with) use ($user)
            {
                $with->where('created_by', $user->id);
            }])
            ->where('id', $id)
            ->where('created_by', $user->id)
            ->first();
        if (!$buyer)
        {
            return response()->json(['data' => ['buyer' => $buyer]], Response::HTTP_NOT_FOUND);
        }
        return response()->json(['data' => ['buyer' => new ApiBuyerShowResource($buyer)]]);
    }

    public function update(ApiCustomerUpdateRequest $request, $id)
    {
        $user = auth('api')->user();

        $validated = $request->validated();
        $customer = Buyer::where('phone_number', $validated['phone_number'])
            ->where('id', $id)
            ->where('created_by', $user->id)
            ->first();
        if ($customer)
        {
//            $name = "{$validated['first_name']} {$validated['last_name']}";

            $name = "{$validated['name']}";
            try
            {
                [$first_name, $last_name] = Str::of($name)->explode(' ', 2);
            } catch (Exception $exception)
            {
                $last_name = ' ';
            }

            [$phone_number, $phone_code, $mobile] = Buyer::splitPhoneNumber($validated['phone_number'], $validated['phone_code']);

            $customer_data = [
                'name' => $name,
                'email' => $validated['email'],
                'phone_code' => $phone_code,
                'phone_number' => $phone_number,
                'class' => Buyer::INDIVIDUAL,
                'rep_name' => $name,
                'rep_phone_code' => $phone_code,
                'rep_phone_number' => $phone_number,
                'is_verified' => 0,
            ];

            if (array_key_exists('password', $validated) && $validated['password'])
            {
                $customer_data['password'] = bcrypt($validated['password']);
            }

            $customer->update($customer_data);
            if ($validated['longitude'] && $validated['latitude'] && $validated['country_id'] && $validated['delivery_address'])
            {
                $address = $customer->addresses()->where('created_by', $user->id)->first();

                if ($address)
                {
                    $address_data = [
//            'buyer_id' => $customer->id,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'delivery_address' => $validated['delivery_address'] ?? ($address->delivery_address ?? null),
                        'city_id' => $validated['city_id'] ?? ($address->city_id ?? null),
                        'country_id' => $validated['country_id'] ?? ($address->country_id ?? null),
                        'county_id' => $validated['county_id'] ?? ($address->county_id ?? null),
                        'phone_number' => $mobile,
                        'email' => $validated['email'] ?? ($address->email ?? null),
                        'latitude' => $validated['latitude'] ?? ($address->latitude ?? null),
                        'longitude' => $validated['longitude'] ?? ($address->longitude ?? null),
                    ];
                    $address->update($address_data);
                }
            }
        }

        return response()->json(['data' => ['customer' => $customer->makeHidden('password') ?? null, 'address' => $address ?? null]]);
    }

    public function delete($id)
    {
        $user = auth('api')->user();

        $customer = Buyer::where('id', $id)
            ->where('created_by', $user->id)
            ->first();
        if ($customer)
        {
//            $address = $customer->addresses()->delete();
            $customer->invoice()->delete();
            if ($customer->delete())
            {
                return response()->json(['data' => ['msg' => 'Customer deleted']]);
            }
        }
        return response()->json(['data' => ['msg' => 'Please check your data']], 403);
    }
}
