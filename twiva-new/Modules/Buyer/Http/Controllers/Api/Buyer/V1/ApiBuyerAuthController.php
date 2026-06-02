<?php

namespace Modules\Buyer\Http\Controllers\Api\Buyer\V1;

use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Buyer\Entities\Buyer;
use Illuminate\Support\Str;
use Modules\Buyer\Http\Requests\Api\Buyer\V1\ApiBuyerLoginRequest;
use Modules\Buyer\Http\Requests\Api\Buyer\V1\ApiBuyerRegisterRequest;
use Modules\Buyer\Http\Resources\Api\Buyer\Auth\ApiBuyerLoginResource;
use Modules\Buyer\Http\Resources\Api\Buyer\Auth\ApiBuyerRegisterResource;
use Modules\Shop\Entities\Shops;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class ApiBuyerAuthController
{
    public function register($account, ApiBuyerRegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::fromShop($account);

        if (!$user) {
            return response()->json(['message' => 'The shop domain field is required', 'errors' => ['shop_domain' => 'The shop domain field is required.']], Response::HTTP_BAD_REQUEST);
        }

        $validated['created_by'] = $user->id;

        $mpesa_user = $validated['mpesa_mini_app'];

        if ($mpesa_user) {
            $customer = Buyer::where('auth_code', $validated['auth_code'])
                ->where('created_by', $user->id)
                ->where('is_external', 1)
                ->first();
            if ($customer) {
                $token = $customer->createToken('MyApp')->accessToken;
                return response()->json(['data' => new ApiBuyerLoginResource($customer, $token)]);
                // return response()->json([
                //     "message" => "The auth code has already been taken.",
                //     "errors" => [
                //         "auth_code" => [
                //             "The auth code has already been taken."
                //         ]
                //     ]
                // ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $customer_data = $this->prepareDataCustomerWithAuthCode($user, $validated);
        } else {
            $customer = Buyer::where(function ($where) use ($validated) {
                if (array_key_exists('email', $validated) and !is_null($validated['email'])) {
                    $where->where('email', $validated['email']);
                } else {
                    $where->where('phone_number', $validated['phone_number']);
                }
            })
                ->where('created_by', $user->id)
                ->where('is_external', 1)
                ->first();
            if ($customer) {
                $token = $customer->createToken('MyApp')->accessToken;
                return response()->json(['data' => new ApiBuyerLoginResource($customer, $token)]);
                // return response()->json([
                //     "message" => "The email or phone number has already been taken.",
                //     "errors" => [
                //         "email_phone_number" => [
                //             "The email or phone number has already been taken."
                //         ]
                //     ]
                // ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $customer_data = $this->prepareDataCustomer($user, $validated);
        }

        // dd($customer_data);

        $customer = $this->createCustomer($customer_data);
        $address = $this->createRelateAddress($user, $validated, $customer);

        // $buyer = Auth::guard('buyer')->loginUsingId($customer->id);
        $token = $customer->createToken('MyApp')->accessToken;

        return response()->json(['data' => new ApiBuyerRegisterResource($customer, $token)]);
        // return response()->json(['data' => ['buyer' => $customer->makeHidden('password'), 'address' => $address ?? null]]);
    }


    public function prepareDataCustomerWithAuthCode($user, $validated)
    {
        //        $name = "{$validated['first_name']} {$validated['last_name']}";
        $name = array_key_exists('name', $validated) ? "{$validated['name']}" : $validated['auth_code'];

        [$phone_number, $phone_code, $mobile] = Buyer::splitPhoneNumber($validated['phone_number'], $validated['phone_code']);

        $email = $validated['email'] ?? null;

        $password = $validated['auth_code'];

        return [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone_code' => $phone_code,
            'phone_number' => $phone_number,
            'class' => Buyer::INDIVIDUAL,
            'rep_name' => $name,
            'rep_phone_code' => $phone_code,
            'rep_phone_number' => $phone_number,
            'created_by' => $user->id,
            'is_verified' => 0,
            'is_external' => 1,
            'auth_code' => $validated['auth_code'],
        ];
    }

    public function prepareDataCustomer($user, $validated)
    {
        //        $name = "{$validated['first_name']} {$validated['last_name']}";
        $name = "{$validated['name']}";

        [$phone_number, $phone_code, $mobile] = Buyer::splitPhoneNumber($validated['phone_number'], $validated['phone_code']);

        $email = $validated['email'] ?? null;

        // $password = bcrypt($validated['password']);
        $password =  $mobile ?? $email;

        return  [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'phone_code' => $phone_code,
            'phone_number' => $phone_number,
            'class' => Buyer::INDIVIDUAL,
            'rep_name' => $name,
            'rep_phone_code' => $phone_code,
            'rep_phone_number' => $phone_number,
            'created_by' => $user->id,
            'is_verified' => 0,
            'is_external' => 1,
        ];
    }


    public function createCustomer($customer_data)
    {

        return Buyer::create($customer_data);
    }

    public function createRelateAddress($account, $validated, $customer)
    {
        $name = $validated['name'] ?? null;
        $email = $validated['email'] ?? null;
        try {
            [$first_name, $last_name] = Str::of($name)->explode(' ', 2);
        } catch (Exception $exception) {
            $last_name = ' ';
        }
        $longitude = $validated['longitude'] ?? null;
        $latitude = $validated['latitude'] ?? null;
        $country_id = $validated['country_id'] ?? null;
        $delivery_address = $validated['delivery_address'] ?? null;
        $address_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'delivery_address' => $delivery_address,
            'city_id' => $validated['city_id'] ?? null,
            'country_id' => $country_id,
            'county_id' => $validated['county_id'] ?? null,
            'phone_number' => $mobile ?? null,
            'email' => $email,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'created_by' => $validated['created_by'] ?? null,
        ];
        if ($longitude && $latitude && $country_id && $delivery_address) {
            $address = $customer->addresses()->create($address_data);
        }
        return $address ?? null;
    }


    public function login($account, ApiBuyerLoginRequest $request)
    {
        $data = $request->validated();
        $user = User::fromShop($account);
        if (!$data['mpesa_mini_app']) {
            $check_field = array_key_exists('mobile', $data) ? 'phone_number' : 'email';
            $value_field = array_key_exists('mobile', $data) ? $data['mobile'] : $data['email'];
            // $login_data_validation = Auth::guard('buyer')->attempt([$check_field => $value_field, 'password' => $data['password']]);

            $buyer = Buyer::where($check_field, $value_field)
                ->where('created_by', $user->id)
                ->where('is_external', 1)
                ->first();

            if (!$buyer) {
                return response()->json(['message' => 'Email and phone are not match'], Response::HTTP_UNAUTHORIZED);
            }
            // $buyer = Auth::guard('buyer')->user();
        } else {
            $buyer = Buyer::where('auth_code', $data['auth_code'])
                ->where('created_by', $user->id)
                ->where('is_external', 1)
                ->first();
            if (!$buyer) {
                return response()->json(['message' => 'user not match'], Response::HTTP_UNAUTHORIZED);
            }
        }

        if ($buyer->created_by != $user->id) {
            return response()->json(['message' => 'user not match'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $buyer->createToken('MyApp')->accessToken;

        return response()->json(['data' => new ApiBuyerLoginResource($buyer, $token)]);
    }
}
