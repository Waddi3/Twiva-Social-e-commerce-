<?php

namespace Modules\Payment\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payment\Entities\WinningPayout;
use Modules\Payment\Http\Requests\StorePayoutRequest;
use Modules\User\Entities\User;
use Modules\User\Helper\EarningUser;
use Symfony\Component\HttpFoundation\Response;

class PayoutRequestController extends Controller {
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('payment::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('payment::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(StorePayoutRequest $request)
    {
        $validated = $request->validated();
        $validated['withdraw_method'] = $validated['withdraw_method'] ?? 'm-pesa';
        $user = auth('api')->user();
        $have_init_request = WinningPayout::where('user_id', $user->id)
            ->where(function (Builder $statusQuery)
            {
                $statusQuery->where('status', WinningPayout::STATUS_INIT)
                    ->orWhere('status', WinningPayout::STATUS_IN_PROGRESS);
            })->exists();
        if ($have_init_request) {
            return response()->json(['data' => ['message' => 'Sorry, we are currently processing your previous request. Please wait a moment and try again.']], Response::HTTP_CONFLICT);
        }
        if ($validated['withdraw_method'] == 'm-pesa')
        {
            if (!$user->mpesa)
            { // when user not have stored mpesa account
                return response()->json(['data' => ['message' => 'Kindly verify your M-Pesa information in your account settings under the payment setup section. Your confirmation ensures accurate payment processing. Thank you']], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($user->type == User::InfluencerType)
            {
                $orders_earnings = EarningUser::calculateEarning($user);
                $campaign_earning = EarningUser::calculateCampaignEarning($user);
                list($transaction_amount, $winnings_payout_paid) = EarningUser::calculateEarningNotPaid($user, $orders_earnings, $campaign_earning);
            }
            else if ($user->type == User::BusinessType)
            {
                $orders_earnings = EarningUser::calculateBusinessEarning($user);
                list($transaction_amount, $winnings_payout_paid) = EarningUser::calculateEarningNotPaid($user, $orders_earnings, 0);
            }
            else
            {
                $transaction_amount = EarningUser::calculatePartnerEarning($user);
            }

            if ($transaction_amount < 10)
            {
                return response()->json(['data' => ['message' => 'Apologies, but it seems you don\'t have any earnings to perform this action.']], Response::HTTP_FORBIDDEN);
            }


            if (!isset($validated['withdraw_to_mobile_number']) || $validated['withdraw_to_mobile_number'] == "true")
            {
                $winning_payout = WinningPayout::create([
                    'user_id'            => $user->id,
                    'phone_number'       => $user->mpesa->mobile,
                    'transaction_amount' => $transaction_amount,
                ]);
            }
            else
            {
                $winning_payout = WinningPayout::create([
                    'user_id'            => $user->id,
                    'till_number'        => $user->mpesa->paybill,
                    'transaction_amount' => $transaction_amount,
                ]);
            }
            return response()->json(['data' => ['message' => 'Your request has been successfully submitted. Please be patient while we process it. Thank you for your patience!']]);
        }
        else
        {
            return response()->json(['data' => ['message' => 'Sorry, Payment method is not defied']], Response::HTTP_CONFLICT);
        }


    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('payment::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('payment::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
