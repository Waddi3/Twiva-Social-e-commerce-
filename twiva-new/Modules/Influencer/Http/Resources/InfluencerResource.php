<?php

namespace Modules\Influencer\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Entities\User;

class InfluencerResource extends JsonResource
{

    private $influencer;

    public function __construct(User $influencer)
    {
        $this->influencer = $influencer;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->influencer->id,
            'email' => $this->influencer->email,
            'name' => $this->influencer->name,
            'type' => $this->influencer->type,
            'gender' => $this->influencer->InfluencerDetails->gender,
            'location' => $this->influencer->InfluencerDetails->location,
            'interest' => $this->influencer->interests,
            'dob' => $this->influencer->InfluencerDetails->dob,
            'points' => $this->influencer->points,
            'description' => $this->influencer->description,
            'profile_image' => $this->influencer->profile_image,
            'content_types' => $this->influencer->contentTypes->map(function ($contentTypes) {
                return collect($contentTypes->toArray())->only(['id', 'name'])
                    ->all();
            }),
            'phone_number' => $this->influencer->phone_number,
            'phone_code' => $this->influencer->phone_code,
            'referral_code' => $this->influencer->referral_code,
            'platforms' => $this->influencer->UserPlatforms->map(function ($platform) {
                return collect($platform->toArray())->only(['platform_id', 'name', 'followers', 'engagements', 'avg_engagements_per_post', 'updated_at', 'social_id'])
                    ->all();
            }),
            'exportShopProductsLink' => route('influencer.product.exportCSV', ['influencer_id' => base64_encode(base64_encode($this->influencer->id))]),
            'unread_community_timelines' => $this->influencer->unreadTimelines(),
            "is_active_notification_email" => $this->influencer->is_active_notification_email,
            "is_active_notification_fcm" => $this->influencer->is_active_notification_fcm,
            "is_admin_for_community" => $this->influencer->is_admin_for_community,
            "de_active_community_at" => $this->influencer->de_active_community_at,
            "banned_at" => $this->influencer->banned_at,
            "community_group_count" => $this->influencer->chatGroups()->count(),

        ];
    }
}
