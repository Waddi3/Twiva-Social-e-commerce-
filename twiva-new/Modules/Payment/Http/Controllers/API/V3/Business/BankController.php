<?php

namespace Modules\Payment\Http\Controllers\API\V3\Business;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payment\Http\Requests\ModifiyBankRequest;
use Modules\User\Repository\BankRepository;

class BankController extends Controller
{
    public function show(Request $request)
    {
        $bankAccount = BankRepository::getBankToShow();
        return response()->json(['data' => $bankAccount]);
    }

    public function modify(ModifiyBankRequest $request)
    {
        $validated = $request->validated();
        BankRepository::modify($validated);
        return response()->json(['data' => 'Bank Account Modified']);
    }
}
