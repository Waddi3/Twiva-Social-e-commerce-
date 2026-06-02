<?php

namespace Modules\Home\Http\Controllers\API\V2;


use Illuminate\Http\Request;
use Modules\Home\Entities\City;
use Modules\Home\Entities\Country;
use Modules\Home\Entities\CountryCode;
use Modules\Home\Entities\State;
use Symfony\Component\HttpFoundation\Response;

class ApiRegionController
{
    public function countryList()
    {
        $country = Country::select([
            "id", "name", "iso3", "iso2", "phonecode", "capital", "native", "region", "subregion", "latitude", "longitude", "emoji", "emojiU",
            "currency", "currency_symbol", "tld", "timezones", "created_at", "updated_at", "flag"
        ])
            ->orderBy('name')
            ->get();
        if (count($country) > 0) {
            $countryData['status'] = 200;
            $countryData['message'] = "Success.";
            $countryData['countries'] = $country;
            return response()->json($countryData);
        } else {
            $error['status'] = Response::HTTP_UNPROCESSABLE_ENTITY;
            $error['message'] = "Country list not found.";
            return response()->json($error);
        }
    }

    public function getCountryCodeList()
    {
        $list = CountryCode::select('id', 'country_code', 'country_name', 'phone_code')->orderBy('country_name')->get();
        return response()->json(['status' => true, 'message' => 'Country code list fetched', 'data' => $list], 200);
    }

    public function stateSelect(Request $request)
    {
        $state = State::where('country_id', $request->country_id)
        ->select(["id", "name", "country_id", "country_code", "iso2", "latitude", "longitude"])
        ->orderBy('name')
        ->get();
        if (count($state) > 0) {
            $stateData['status'] = 200;
            $stateData['message'] = "Success.";
            $stateData['states'] = $state;
            return response()->json($stateData);
        } else {
            $error['status'] = Response::HTTP_UNPROCESSABLE_ENTITY;
            $error['message'] = "State list not found.";
            return response()->json($error);
        }
    }

    public function citySelect(Request $request)
    {
        $City = City::where('state_id', $request->state_id)->select(["id", "name", "state_id", "state_code", "country_id", "country_code", "latitude", "longitude"])
        ->orderBy('name')
        ->get();
        if (count($City) > 0) {
            $cityData['status'] = 200;
            $cityData['message'] = "Success.";
            $cityData['cities'] = $City;
            return response()->json($cityData);
        } else {
            $error['status'] = Response::HTTP_UNPROCESSABLE_ENTITY;
            $error['message'] = "City list not found.";
            return response()->json($error);
        }
    }

    public function cityCountry(Request $request)
    {
        $City = City::where('country_id', $request->country_id)->select(["id", "name", "state_id", "state_code", "country_id", "country_code", "latitude", "longitude"])
        ->orderBy('name')
        ->get();
        if (count($City) > 0) {
            $cityData['status'] = 200;
            $cityData['message'] = "Success.";
            $cityData['cities'] = $City;
            return response()->json($cityData);
        } else {
            $error['status'] = Response::HTTP_UNPROCESSABLE_ENTITY;
            $error['message'] = "City list not found.";
            return response()->json($error);
        }
    }
}
