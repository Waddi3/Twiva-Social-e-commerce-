<?php

namespace Modules\Business\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Business\Entities\BusinessShipping;
use Modules\Business\Entities\Company;
use Modules\Business\Http\Requests\EditBusinessDetailsRequest;
use Modules\Business\Http\Requests\EditBusinessShippingRequest;
use Modules\Business\Http\Resources\BusinessResource;
use Modules\User\Entities\User;

class ApiBusinessProfileController extends Controller
{
    public function getBusinessDetails(Request $request)
    {
        $user = auth('api')->user();
        return response()->json(['data' => new BusinessResource($user)]);
    }

    public function editBusinessDetails(EditBusinessDetailsRequest $request)
    {
        $business = auth('api')->user();
        $data = $request->validated();
        $userData = [
            'name' => $data['name'],
            'phone_number' => $data['phone_number'],
            'phone_code' => $data['phone_code'],
            'description' => $data['description'],
        ];
        if (isset($data['logo']))
        {
            $data['logo'] = $data['logo']->store(Company::IMAGE_PATH, 'public');
            $data['logo'] = str_replace(User::IMAGE_PATH, '', $data['logo']);
            $userData['profile_image'] = $data['logo'];
        }
        unset($data['phone_code'], $data['phone_number'], $data['name'], $data['description'], $data['logo'],);
        $business->update($userData);
        $business->businessDetails()->update($data);
        return response()->json(
            [
                'status' => true,
                'message' => 'Business details updated successfully',
                'logo_link' => $business->fresh()->profileImage
            ]);


    }

    public function editBusinessShipping(EditBusinessShippingRequest $request) {
        $data = $request->validated();
        $data['business_id'] = auth('api')->user()->id;
        $businessShipping = BusinessShipping::where('business_id', $data['business_id'])->first();
        if( !$businessShipping ) {
            $businessShipping = BusinessShipping::create($data);
        }
        else {
            $businessShipping->update($data);
        }

        return response()->json([
            'status' => true,
            'message' => 'Shipping settings has updated'
        ]);
    }
}
