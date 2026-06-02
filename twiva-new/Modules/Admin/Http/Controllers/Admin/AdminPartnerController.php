<?php

namespace Modules\Admin\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Admin\Events\NewUserRegistered;
use Modules\Admin\Http\Requests\AdminCreatePartnerRequest;
use Modules\Admin\Http\Requests\AdminUpdatePartnerRequest;
use Modules\Admin\Http\Requests\AdminUploadShippingRequest;
use Modules\Business\Entities\Company;
use Modules\Home\Entities\City;
use Modules\Home\Entities\CountryCode;
use Modules\Home\Entities\State;
use Modules\Order\Entities\ShippingRate;
use Modules\Order\Imports\ShippingRatesImport;
use Modules\User\Entities\User;
use Modules\User\Repository\UserRepository;

class AdminPartnerController extends Controller
{

    public function index()
    {
        $partners = User::where('type', 'Partner')
            ->orderBy('id', 'desc')
            ->paginate(10);
        //$partners = $partners->setCollection($partners->getCollection()->sortBy('ordersCount'));
        return view('admin::partner.index', ['users' => $partners]);
    }

    public function create()
    {
        $countries = CountryCode::all();
        return view('admin::partner.create', compact('countries'));
    }

    public function store(AdminCreatePartnerRequest $request)
    {
        $data = $request->validated();

        $data['type'] = User::PartnerType;
        $data['password'] = bcrypt($data['password']);
        $data['referral_code'] = UserRepository::createReferralCode();
        $partnerData['contact_name'] = $data['contact_name'];
        $partnerData['country'] = $data['country'];
        $partnerData['city'] = $data['city'];
        $partnerData['state'] = $data['state'];
        $partnerData['area'] = $data['area'];
        unset($data['contact_name'], $data['country'], $data['city'], $data['state'], $data['area']);
        if (isset($data['logo']))
        {
            $data['logo'] = $data['logo']->store(User::IMAGE_PATH, 'public');
            $data['profile_image'] = str_replace(User::IMAGE_PATH, '', $data['logo']);
        }
        unset($data['logo']);
        $partner = User::create($data);
        $partner->businessDetails()->create($partnerData);

        event(new NewUserRegistered($partner, $data['password']));
        $partner->update([
            'is_profile_completed' => 1,
            'status' => true,
            'is_verified' => 1,
            'mobile_verified_at' => now(),
        ]);
        return redirect()->route('admin.partner.index')->with('message', 'Partner Created Successfully');
    }

    public function edit($id)
    {
        $states = '';
        $cities = '';
        $partner = User::findOrFail($id);
        $company = Company::with('countryDetails', 'stateDetails', 'cityDetails')->where('user_id', $id)->first();
        $countries = CountryCode::all();
        if ($company && $company->country) {
            $states = State::where('country_id', $company->country)->get();
        }
        if ($company && $company->state) {
            $cities = City::where('state_id', $company->state)->get();
        }

        //dd($states->toArray());
        $data = [
            'id' => $partner->id,
            'partner' => $partner,
            'company' => $company,
            'countries' => $countries,
            'cities' => $cities,
            'states' => $states,
        ];
        return view('admin::partner.edit', $data);
    }

    public function update(AdminUpdatePartnerRequest $request, $id)
    {
        $data = $request->validated();
        $partner = User::findOrFail($id);
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'phone_code' => $data['phone_code'],
            'description' => $data['description'],
        ];
        if (isset($data['logo'])) {
            $data['logo'] = $data['logo']->store(Company::IMAGE_PATH, 'public');
            $data['logo'] = str_replace(User::IMAGE_PATH, '', $data['logo']);
            $userData['profile_image'] = $data['logo'];
        }

        unset($data['phone_code'], $data['phone_number'], $data['name'], $data['description'], $data['logo'], $data['image'], $data['email']);
        $partner->update($userData);
        $partner->businessDetails()->update($data);

        return redirect()->route('admin.partner.index')->with('message', 'Partner Updated Successfully');
    }

    public function upload(AdminUploadShippingRequest $request) {
        $data = $request->validated();
        $partner = User::find($data['partner_id']);
        if( !$partner ) {
            return response()->json([
                'status' => false,
                'message' => 'Partner not found'
            ]);
        }
        // Delete
        ShippingRate::where('partner_id', $partner->id)->delete();

        // Import
        $shippingRatesImport = new ShippingRatesImport();
        Excel::import($shippingRatesImport, request()->file('file'));
        if( count($shippingRatesImport->data) ) {
            foreach($shippingRatesImport->data as $rateItem) {
                $sourceCity = City::where('name', $rateItem['source_city_name'])
                    ->select('id')
                    ->first();
                $destinationCity = City::where('name', $rateItem['destination_city_name'])
                    ->select('id')
                    ->first();

                if( $sourceCity && $destinationCity ) {
                    ShippingRate::create([
                        'partner_id' => $partner->id,
                        'source_city_id' => $sourceCity->id,
                        'destination_city_id' => $destinationCity->id,
                        'rate' => $rateItem['rate']
                    ]);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Done'
        ]);
    }

}
