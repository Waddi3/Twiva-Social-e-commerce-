<?php

namespace Modules\Campaign\Http\Resources\Business;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Platform\Entities\WhatsAppGroup;
use Modules\User\Entities\User;

class ShowCampaignCollection extends ResourceCollection
{
    private $campaign;

    public function __construct($campaign)
    {

        $this->campaign = $campaign;
    }

    public function toArray($request)
    {
        //        dd($this->campaign);
        return [
            'id' => $this->campaign->id,
            'post_name' => $this->campaign->post_name,
            'post_image' => isset($this->campaign->Images[0]) ? asset($this->campaign->Images[0]->mediaPath) : '',
            'expiration_date' => $this->campaign->expiration_date ?? null,
            'status' => 'OnGoing',
            'influencers' => $this->getInfluencersDetails(),
            'approve_status' => $this->campaign->approve_status,
            'products' => $this->campaign->products,
        ];
    }

    private function getInfluencersDetails()
    {
        return $this->campaign->influencersInPost->map(function ($influencerInvitatioon) {
            if ($influencerInvitatioon->user_type == (new User())->getMorphClass()) {
                if ($influencerInvitatioon->influencer) {
                    $influencerInvitatioon->invitation_id = $influencerInvitatioon->id;
                    $influencerInvitatioon->final_price = $influencerInvitatioon?->influencerPriceObject()?->final_price;
                    $influencerInvitatioon->id = $influencerInvitatioon->influencer->id;
                    $influencerInvitatioon->name = $influencerInvitatioon->influencer->name;
                    $influencerInvitatioon->status = $influencerInvitatioon->statusStringForBusiness;
                    $influencerInvitatioon->avatar = $influencerInvitatioon->influencer->profile_image ?? "";
                    $influencerInvitatioon->description = $influencerInvitatioon->influencer->description ?? 'N/A';
                    $influencerInvitatioon->location = isset($influencerInvitatioon->influencer->InfluencerDetails) ? $influencerInvitatioon->influencer->InfluencerDetails->location : 'N/A';
                    $influencerInvitatioon->buiness_influencer_price = $influencerInvitatioon->businessInfluencerPrice;
                    $influencerInvitatioon->influencer_price = $influencerInvitatioon->buinessinfluencerPrice;
                    $influencerInvitatioon->complementry_item = $influencerInvitatioon->influencerComplementryItem;
                    $influencerInvitatioon->platforms = $influencerInvitatioon->influencer->UserPlatforms->map(function ($platform) {
                        return collect($platform->toArray())
                            ->only(['platform_id', 'name', 'followers', 'engagements', 'avg_engagements_per_post', 'updated_at'])
                            ->all();
                    });
                }
            } elseif ($influencerInvitatioon->user_type == (new WhatsAppGroup())->getMorphClass()) {
                if ($influencerInvitatioon->whatsAppGroup) {
                    $influencerInvitatioon->invitation_id = $influencerInvitatioon->id;
                    $influencerInvitatioon->final_price = $influencerInvitatioon?->influencerPriceObject()?->final_price;
                    $influencerInvitatioon->id = $influencerInvitatioon->whatsAppGroup->id;
                    $influencerInvitatioon->name = $influencerInvitatioon->whatsAppGroup->name;
                    $influencerInvitatioon->status = $influencerInvitatioon->statusStringForBusiness;
                    $influencerInvitatioon->avatar = "";
                    $influencerInvitatioon->description = 'N/A';
                    $influencerInvitatioon->location = $influencerInvitatioon->whatsAppGroup->city->name ?? "";
                    $influencerInvitatioon->buiness_influencer_price = $influencerInvitatioon->businessInfluencerPrice;
                    $influencerInvitatioon->influencer_price = $influencerInvitatioon->buinessinfluencerPrice;
                    $influencerInvitatioon->complementry_item = $influencerInvitatioon->influencerComplementryItem;
                    $influencerInvitatioon->platforms = collect([
                        [
                            'platform_id' => 0,
                            'name' => $influencerInvitatioon->whatsAppGroup->name,
                            'followers' => $influencerInvitatioon->whatsAppGroup->number_of_people,
                            'engagements' => 0,
                            'avg_engagements_per_post' => 0,
                            'updated_at' => $influencerInvitatioon->whatsAppGroup->updated_at,
                        ]
                    ]);
                }
            }
            return collect($influencerInvitatioon->toArray())
                ->only('invitation_id', 'final_price', 'id', 'user_type', 'name', 'status', 'avatar', 'description', 'location', 'platforms', 'buiness_influencer_price', 'influencer_price', 'reason', 'complementry_item')
                ->all();
        });
    }
}
