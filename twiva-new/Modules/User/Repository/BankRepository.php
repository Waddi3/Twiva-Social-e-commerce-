<?php


namespace Modules\User\Repository;


use Modules\Payment\Entities\BankAccounts;
use function auth;

class BankRepository
{
    public static function getBankToShow()
    {
        $userType = auth('api')->user()->type ;
        return BankAccounts::where(['user_type' => $userType, 'user_id' => auth()->id()])
            ->with(['beneficiaryCountry' => function ($beneficiaryCountry) {
                return $beneficiaryCountry->select('id', 'name');
            }, 'bank' => function ($bank) {
                return $bank->select('id', 'bank_name');
            }, 'bankCountry' => function ($bankCountry) {
                return $bankCountry->select('id', 'name');
            }])
            ->first();
    }

    public static function modify($data)
    {
        $data['user_type'] = auth('api')->user()->type;
        BankAccounts::updateOrCreate([
            'user_id' => auth('api')->id(),
            'user_type' => $data['user_type']
        ],
            $data
        );
    }
}