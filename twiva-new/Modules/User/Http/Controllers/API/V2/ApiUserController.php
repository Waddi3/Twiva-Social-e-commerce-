<?php

namespace Modules\User\Http\Controllers\API\V2;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;

class ApiUserController extends Controller
{
    public function logout(Request $request)
    {
        try {
            //login user details
            $user = auth('api')->user();
            auth('api')->user()->token()->revoke();
            auth('api')->user()->token()->delete();
            $responseData['success'] = Response::HTTP_OK;
            $responseData['message'] = 'You have been logged out successfully.';
            return response()->json($responseData);
        } catch (Exception $e) {
            $error['status'] = 500;
            $error['message'] = $e->getMessage();
            return response()->json(['failure' => $error]);
        }
    }

    public function checkIfEmailExists(Request $request)
    {
        if (User::withTrashed()->where('email', \request('email'))->exists()) {
            return response()->json(['status' => false, 'message' => 'Email already exists. Please login.', 'data' => ['message' => 'Email already exists. Please login.']], 409);
        }
        return response()->json(['status' => true, 'message' => 'This email can be used.'], 200);
    }

    public function RedirectLogin()
    {
        return redirect('login');
    }
}
