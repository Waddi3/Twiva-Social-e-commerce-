<?php


namespace Modules\Campaign\Repository\Business;


use Illuminate\Database\Eloquent\Builder;
use Modules\Campaign\Entities\Post;
use Modules\Campaign\Entities\PostImage;
use Modules\Campaign\Entities\PostPrice;
use Modules\Campaign\Entities\PostUser;
use Modules\Influencer\Entities\InfluencerTransactionsHistory;
use Modules\Notification\Notifications\Influencer\HiredForCampaignNotification;
use Modules\Notification\Notifications\Influencer\InvitationToNewCampaignNotification;
use Modules\Platform\Entities\WhatsAppGroup;
use Modules\Products\Repositories\Influencer\ProductRepository;
use Modules\User\Entities\User;
use function auth;

class CampaignRepository
{
    public function create(array $data)
    {
        $user_type = array_key_exists('influencers', $data) ? ((new User)->getMorphClass()) : ((new WhatsAppGroup())->getMorphClass());
        $data['user_id'] = auth('api')->id();
        $data['what_to_include'] = $data['what_to_include'] ?? "";
        $data['hashtag'] = $data['hashtag'] ?? "";
        $data['user_type'] = $user_type;
        $post = Post::create($data);
        if (isset($data['media'])) {

            foreach ($data['media'] as $media) {
                PostImage::create([
                    'media' => str_replace(PostImage::IMAGEPATH . '/', '', $media->store(PostImage::IMAGEPATH, 'public')),
                    'post_id' => $post->id,
                ]);
            }
        }
        if (array_key_exists('influencers', $data) || array_key_exists('whatsapp_groups', $data)) {
            $data_campaigns = array_key_exists('influencers', $data) ? $data['influencers'] : $data['whatsapp_groups'];
            foreach ($data_campaigns as $influencer) {
                $invitation = PostUser::create(
                    [
                        'user_id' => $influencer['id'],
                        'user_type' => $user_type,
                        'post_id' => $post->id,
                        'status' => PostUser::PENDING_STATUS,
                        'is_submit' => 1,
                        'is_resend' => 0
                    ]
                );
                PostPrice::create(
                    [
                        'user_id' => auth('api')->id(),
                        'infulancer_id' => $influencer['id'],
                        'user_type' => $user_type,
                        'post_id' => $post->id,
                        'status' => 1,
                        'price' => $influencer['price'],
                        'complementry_item' => $influencer['complementry_item'] ?? null,
                        'is_resend' => 0,
                        'final_price' => $influencer['price'],
                    ]
                );
                if (array_key_exists('influencers', $data)) {
                    $influencer = User::find($influencer['id']);
                } elseif (array_key_exists('whatsapp_groups', $data)) {
                    $influencer = WhatsAppGroup::with('user')->find($influencer['id'])?->user;
                }
                $influencer->notify(new InvitationToNewCampaignNotification($invitation));
            }
        } else {
            $post->update(['post_type' => Post::BROADCAST]);
            // TODO::H send email to all active influencers
        }
        if (array_key_exists('products', $data) and $data['products']) {
            // unique products ids?
            $post->products()->sync($data['products']);
        }
        return $post->id;
    }

    public static function payForCampaign($campaign_id)
    {
        $influencersInvitations = PostUser::where('post_id', $campaign_id)
            ->where('status', PostUser::AppliedStatus)
            ->with('post:id,user_id,user_type,post_type,status', 'post.products', 'influencer', 'influencer.influencerShopProducts')
            ->get();
        foreach ($influencersInvitations as $influencerInvitation) {
            $influencerInvitation->update(['status' => PostUser::HiredStatus]);
            if ($influencerInvitation->user_type == (new User())->getMorphClass()) {
                $influencer = $influencerInvitation->influencer;
            } elseif ($influencerInvitation->user_type == (new WhatsAppGroup())->getMorphClass()) {
                $influencer = $influencerInvitation->whatsAppGroup->user;
            }
            $influencer->notify(new HiredForCampaignNotification($influencerInvitation));
            PostPrice::where('infulancer_id', $influencerInvitation->user_id)
                ->where('user_type', $influencerInvitation->user_type)
                ->where('post_id', $influencerInvitation->post_id)
                ->update(['status' => PostUser::HiredStatus]);
            InfluencerTransactionsHistory::create(
                [
                    'user_id' => $influencerInvitation->id,
                    'transaction_amount' => $influencerInvitation->businessInfluencerPrice,
                    'request_type' => InfluencerTransactionsHistory::BUSINESS_PAY_FOR_CAMPAIGN,
                    'request_id' => $campaign_id,
                    'is_active' => 0,
                ],
            );
            self::addProductCampaignToHiredInfluencer($influencer, $influencerInvitation->post->products);
        }
    }

    private static function addProductCampaignToHiredInfluencer($influencer, $products)
    {
        $product_data = self::prepareProductData($influencer, $products);
        $productRepository = new ProductRepository();
        $productRepository->storeShopProduct($influencer->shop->id, $product_data, $influencer->id);
    }

    private static function prepareProductData($influencer, $products)
    {
        $data = [];
        foreach ($products as $product) {
            if (!$influencer->influencerShopProducts->firstWhere('product_id', $product->id)) {
                $data['products'][] = [
                    'product_id' => $product->id,
                    'product_title' => $product->name,
                    'product_price' => $product->product_price,
                    'product_description' => $product->description,
                ];
            }
        }
        return $data;
    }
}
