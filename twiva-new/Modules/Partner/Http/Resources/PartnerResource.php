<?php

namespace Modules\Partner\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Entities\User;

class PartnerResource extends JsonResource
{

    private $partner;

    public function __construct(User $partner)
    {
        $this->partner = $partner;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->partner->id,
            'email' => $this->partner->email,
            'name' => $this->partner->name,
            'type' => $this->partner->type,
            'points' => $this->partner->points,
            'referral_code' => $this->partner->referral_code,
            'description' => $this->partner->description,
            'profile_image' => $this->partner->profile_image,
            'phone_number' => $this->partner->phone_number,
            'phone_code' => $this->partner->phone_code,
        ];
    }
}
