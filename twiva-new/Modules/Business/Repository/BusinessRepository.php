<?php


namespace Modules\Business\Repository;


use Modules\User\Entities\User;
use Modules\User\Repository\UserRepository;

class BusinessRepository
{
    public static function createBusiness($data)
    {
        $data['type'] = User::BusinessType;
        $data['password'] = bcrypt($data['password']);
        $data['referral_code'] = UserRepository::createReferralCode();
        $businessData['contact_name'] = $data['contact_name'];
        $businessData['country'] = $data['country'];
        $businessData['city'] = $data['city'];
        $businessData['state'] = $data['state'];
        $businessData['area'] = $data['area'];
        unset($data['contact_name'], $data['country'], $data['city'], $data['state'], $data['area']);
        if (isset($data['logo']))
        {
            $data['logo'] = $data['logo']->store(User::IMAGE_PATH, 'public');
            $data['profile_image'] = str_replace(User::IMAGE_PATH, '', $data['logo']);
        }
        unset($data['logo']);
        $business = User::create($data);
        $business->businessDetails()->create($businessData);
        $business->businessShipping()->create([
            'shipping_by_partner' => 1,
            'has_shipping_fees' => 0
        ]);

        return $business;
    }
}
