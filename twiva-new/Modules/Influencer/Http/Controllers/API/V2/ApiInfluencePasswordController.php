<?php

namespace Modules\Influencer\Http\Controllers\API\V2;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Modules\Home\Helper\Helper;
use Modules\User\Entities\Otp;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class ApiInfluencePasswordController extends Controller
{
    public function forgotPasswordOtp(Request $request)
    {
        ini_set('max_execution_time', -1);
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255',
            ]);

            if ($validator->fails()) {

                return response()->json(['status' => Response::HTTP_BAD_REQUEST, 'error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            } else {

                $email = $request->email;
                $count = User::select('id')->where('email', '=', $email)->count();
                if ($count > 0) {
                    // $otpCode = Helper::randomOtp();
                    $otpCode = mt_rand(100000, 999999);
                    $otpType = "forget-password";
                    $data = array(
                        'type' => $otpType,
                        'email' => $request->email,
                        'otp' => $otpCode,
                    );
                    // $otpId = Otp::create($data);
                    // $encryptedId = encrypt($otpId->id);
                    // $req_email = $request->email;
                    // $mailData = array('subject' => 'OTP', 'email' => $request->email, 'otp' => $otpCode);
                    // $this->sendSMTPMail('emails.sendOtp', $mailData);

                    try {
                        $otpId = Otp::create($data);
                        $encryptedId = encrypt($otpId->id);

                        //Mail for Otp

                        // $mailData = array('subject' => 'OTP', 'email' => $request->email, 'otp' => $otpCode,);

                        $req_email = $request->email;
                        $details = User::where('email', $req_email)->first();
                        $username = $details->name;


                        Mail::send('emails.sendOtp', ['name' => $username, 'msg' => $otpCode],
                            function ($m) use ($req_email) {
                                $m->from(config('mail.from.address'), config('app.name'));
                                $m->to($req_email)->subject('OTP');
                            });
                    } catch (Exception $e) {
                        //$responseData['success'] = $this->badRequest;


                        $responseData['message'] = 'Some thing wrong to send email.';
                        $responseData['user'] = array(
                            'otp_id' => $encryptedId,
                            'email' => $email,
                            'otp' => $otpCode
                        );
                        return response()->json(['failure' => $responseData]);
                    }
                    $responseData['user'] = array(
                        'otp_id' => $encryptedId,
                        'email' => $email,
                    );
                    $responseData['success'] = Response::HTTP_OK;
                    $responseData['message'] = 'Check your mail to get OTP';
                    return response()->json($responseData);
                } else {

                    return response()->json(['code' => 400, 'message' => 'This email address could not be found. Please try again.'], 400);
                    //   $error['status'] = $this->badRequest;
                    //   $error['message']  = 'This email address could not be found. Please try again.';
                    //   return response()->json(['failure'=>$error]);
                }
            }
        } catch (Exception $e) {
            $error['status'] = 500;
            $error['message'] = $e->getMessage();
            return response()->json(['failure' => $error]);
        }
    }

    public function forgotPasswordOtpVerify(Request $request)
    {
        try {
            // Otp validate
            try {
                $decryptId = decrypt($request->otp_id);
            } catch (Exception $e) {
                // return $this->returnJsonResponse('failure', [
                //     'otp_id' => ["Please enter valid otp id"]
                // ], 'Please enter valid otp id.', 'not_found');
                //                $error['status'] = $this->badRequest;
                //                  $error['message']  = 'Please enter valid otp id';
//		    return response()->json(['failure'=>$error]);
                return response()->json(['code' => 400, 'message' => 'Please enter valid OTP ID.'], 400);

            }
            $email = $request->email;

            $data = Otp::where(array('id' => $decryptId, 'email' => $email))->first();
            if (isset($data->id)) {
                //Otp expair time Check.
                $expire = Helper::expireOtp($data);
                $response = array();
                if ($expire == True) {

                    $newPws = bcrypt($request->password);
                    $updateData = array(
                        'password' => $newPws,
                        'is_verified' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    );
                    User::where(['email' => $email])->update($updateData);
                    $userId = User::select('id')->where(['email' => $email])->first();
                    //Mail for Pws Update.
                    $mailData = array('subject' => 'Change pwssword', 'email' => $email,);
                    //$this->sendSMTPMail('changepws', $mailData);
                    //LoginLog::where([['user_id', '=', $userId->id], ['wrong_atempt', '!=', null]])->delete();
                    $response['status'] = 'success';
                    $response['code'] = Response::HTTP_OK;
                    $response['message'] = 'Password changed successfully.';
                } else {
                    //   $response['status'] = 'failure';
                    //   $response['code'] = $this->badRequest;
                    //  $response['message'] = 'OTP is expired.Please resend and try again.';
                    return response()->json(['code' => 400, 'message' => 'OTP is expired. Please try again.'], 400);
                }
                $delrecord = Otp::find($decryptId);
                $delrecord->delete();
                return response()->json($response);
            } else {
                $error['status'] = Response::HTTP_UNPROCESSABLE_ENTITY;
                $error['message'] = "Invalid inputs.";
                // return response()->json(['failure'=>$error]);
                return response()->json(['code' => 400, 'message' => 'Invalid inputs.'], 400);
            }
        } catch (Exception $e) {
            //$error['status'] = 500;
            // $error['message']  = $e->getMessage();
            // return response()->json(['failure'=>$error]);
            return response()->json(['code' => 400, 'message' => 'Internal server error.'], 400);
        }
    }

    public function OtpVerify(Request $request)
    {
        try {
            $decryptId = decrypt($request->otp_id);
        } catch (Exception $e) {
            $error['status'] = Response::HTTP_UNPROCESSABLE_ENTITY;
            $error['message'] = 'Please enter valid otp id';
            return response()->json(['failure' => $error]);
        }
        $email = $request->email;
        $otpCode = $request->otp;

        $data = Otp::where(['id' => $decryptId, 'otp' => $otpCode, 'email' => $email])->first();

        if (isset($data->id)) {
            $response['status'] = 'Success';
            $response['code'] = Response::HTTP_OK;
            $response['message'] = 'Otp verified successfully.';
            $delrecord = Otp::find($decryptId);
            return response()->json($response);
        } else {
            $data = User::where(['otp' => $otpCode, 'otp' => $otpCode, 'email' => $email])->first();
            if (isset($data->id)) {
                $response['status'] = 'Success';
                $response['code'] = Response::HTTP_OK;
                $response['message'] = 'Otp verified successfully.';
                $delrecord = Otp::find($decryptId);
                return response()->json($response);
            }
        }
        return response()->json(['code' => 400, 'message' => 'OTP is invalid.'], 400);

    }
}
