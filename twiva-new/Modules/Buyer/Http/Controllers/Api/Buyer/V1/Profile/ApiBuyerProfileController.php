<?php

namespace Modules\Buyer\Http\Controllers\Api\Buyer\V1\Profile;

use Modules\Buyer\Http\Requests\Api\Buyer\V1\Profile\ApiBuyerProfileAddressCreateRequest;
use Modules\Buyer\Http\Requests\Api\Buyer\V1\Profile\ApiBuyerProfileAddressUpdateRequest;
use Modules\Buyer\Http\Requests\Api\Buyer\V1\Profile\ApiBuyerProfileUpdateRequest;
use Modules\Buyer\Http\Resources\Api\Buyer\ApiBuyerProfileAddressResource;
use Modules\Buyer\Http\Resources\Api\Buyer\ApiBuyerProfileResource;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class ApiBuyerProfileController
{
    public function profile($account)
    {
        $buyer = auth('buyer-api')->user();
        return response()->json(['buyer' => new ApiBuyerProfileResource($buyer)]);
    }

    public function updateProfile($account, ApiBuyerProfileUpdateRequest $request)
    {
        $buyer = auth('buyer-api')->user();
        $validated = $request->validated();
        $buyer->update($validated);
        return response(['data' => 'updated']);
    }

    public function addresses($account)
    {
        $buyer = auth('buyer-api')->user();
        $partners = User::where('type', User::PartnerType)
            ->select('id', 'name')
            ->get();
        return response()->json([
            'data' => ApiBuyerProfileAddressResource::collection($buyer->addresses()->latest()->get()),
            'partners' => $partners
        ]);
    }

    public function createAddress($account, ApiBuyerProfileAddressCreateRequest $request)
    {
        $buyer = auth('buyer-api')->user();
        $validated = $request->validated();
        $address = $buyer->addresses()->create($validated);
        if ($address) {
            return response(['data' => ['message' => 'created', 'address' => ['id' => $address->id]]], Response::HTTP_CREATED);
        }
        return response(['error' => 'Please check your data'], 400);
    }

    public function updateAddress($account, $id, ApiBuyerProfileAddressUpdateRequest $request)
    {
        $buyer = auth('buyer-api')->user();
        $validated = $request->validated();
        $address = $buyer->addresses()->where('id', $id)->first();
        if ($address) {
            $address->update($validated);
            return response(['data' => 'updated']);
        }
        return response(['error' => 'Please check your data'], 400);
    }

    public function deleteAddress($account, $id)
    {
        $buyer = auth('buyer-api')->user();
        $address = $buyer->addresses()->where('id', $id)->delete();
        if ($address) {
            return response(['data' => 'deleted']);
        }
        return response(['error' => 'Please check your data'], 400);
    }
}
