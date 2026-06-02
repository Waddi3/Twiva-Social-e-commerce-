<?php

namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;

class UserCompletedProfile
{
    use SerializesModels;

    public $invited_id;

    public function __construct($invited_id)
    {
        $this->invited_id = $invited_id;
    }


}
