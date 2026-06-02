<?php

namespace Modules\Payment\Http\Controllers\API\V2;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Modules\Business\Entities\BusinessMpesa;
use Modules\Payment\Entities\Bank;
use Modules\Payment\Entities\InfluencerBankDetails;
use function auth;

class ApiBankController extends Controller
{
    public function bankDetails()
    {
        $user = auth('api')->user();
        try {
            $bank_detais = InfluencerBankDetails::with('bank_detais')->where('influencer_id', $user->id)->first();
            if ($bank_detais) {
                return response()->json(['status' => true, 'message' => 'Bank details fetched', 'data' => $bank_detais], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'No bank details found'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function bankList()
    {
        //        $user = auth('api')->user();
        try {
            $bank_list = Bank::all();
            return response()->json(['status' => true, 'message' => 'Bank list fetched', 'data' => $bank_list], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function bankSetup(Request $request)
    {
        $data = $request->all();
        try {
            $validator = Validator::make($data, [
                'bank_id' => 'required',
                'account_number' => 'required',
                'recipient_name' => 'required',
                'ifsc_code' => 'required'
            ]);
            if ($validator->fails())
                return response()->json(['status' => false, 'error' => $validator->errors()], 400);
            $data['influencer_id'] = auth('api')->id();
            // $user = InfluencerBankDetails::create($data);
            InfluencerBankDetails::updateOrCreate(
                ['influencer_id' => $data['influencer_id']],
                ['bank_id' => $data['bank_id'], 'account_number' => $data['account_number'], 'recipient_name' => $data['recipient_name'], 'ifsc_code' => $data['ifsc_code'], 'branch_name' => $data['branch_name'], 'city' => $data['city']]
            );
            return response()->json(['status' => true, 'message' => 'Bank details added'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

    //Business Mobile Number
    public function BusiMpesa(Request $request)
    {
//        return response()->json(['status' => false, 'message' => 'will be back soon'], 422, []);

        if (auth('api')->user()?->pass_withdraw) {
            return response()->json(['status' => true, 'message' => 'M-Pesa Details not updated'], 200, []);
        }
        BusinessMpesa::updateOrCreate(
            ['busi_ID' => auth('api')->id()],
            [
                'mobileNumber' => $request->has('mnumber') ? \request('mnumber') : null,
                'name' => \request('name'),
                'businame' => \request('businame'),
                'paybill' => $request->has('paybill') ? \request('paybill') : null,
                'created_at' => Carbon::now()
            ]
        );
        return response()->json(['status' => true, 'message' => 'M-Pesa Details updated successfully'], 200, []);
    }

    public function BusiMpesaGet()
    {
        $user = auth('api')->user();
        $data = BusinessMpesa::where('busi_ID', $user->id)->first();
        $user_data = [
            'id' => $user->id,
            'name' => $user->name,
            'mobile' => $user->mobile,
            'phone_code' => $user->phone_code,
            'phone_number' => $user->phone_number,
        ];
        return response()->json(['status' => true, 'message' => $data, 'user_data' => $user_data], 200, []);
    }
}
