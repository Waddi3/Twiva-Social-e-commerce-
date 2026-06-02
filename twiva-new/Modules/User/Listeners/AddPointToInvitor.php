<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\User\Entities\User;
use Modules\User\Entities\UserReferalCode;
use Modules\User\Entities\UserReferralCode;
use Modules\User\Events\UserCompletedProfile;

class AddPointToInvitor
{

    public function __construct()
    {
        //
    }


    public function handle(UserCompletedProfile $event)
    {
        $userReferralCode = UserReferralCode::where(['invited_id' => $event->invited_id, 'is_activated' => 0])->first();
        if ($userReferralCode)
        {
            $userReferralCode->update(['is_activated' => 1]);

            $invitor = User::where(['id' => $userReferralCode->invitor_id])->first();
//            $invitor->update(['points' => $invitor->points + 5]);
            $invited = User::where(['id' => $userReferralCode->invited_id])->first();
            $invited->update(['is_profile_completed' => 1]);

//            Log::debug('UserCompletedProfile', ['$event' => $event, 'userReferralCode' => $userReferralCode, 'invitor' => $invitor]);
        }

    }
}
