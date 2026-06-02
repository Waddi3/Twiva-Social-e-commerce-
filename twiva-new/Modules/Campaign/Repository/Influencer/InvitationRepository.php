<?php


namespace Modules\Campaign\Repository\Influencer;


use Modules\Campaign\Entities\Post;
use Modules\Campaign\Entities\PostPrice;
use Modules\Campaign\Entities\PostUser;
use Modules\Campaign\Enums\ApproveStatus;

class InvitationRepository
{


    public function getInvitationsToIndex($keyword = null, $status = null)
    {
        $invitations = PostUser::select('id', 'post_id', 'user_id', 'user_type', 'status', 'created_at')
            ->with(
                [
                    'Post' => function ($postQuery) {
                        return $postQuery->select('id', 'post_name', 'price_per_post')
                            ->with(['business' => function ($businessQuery) {
                                $businessQuery->select('id', 'name');
                            }])
                            ->withCount('products')
                            ->where('approve_status', ApproveStatus::Approved->value);
                    },
                    'influencerPriceBase' => function ($query) {
                        return $query->select('id', 'post_id', 'user_id', 'infulancer_id', 'price', 'final_price');
                    }
                ]
            )
            ->latest()
            //            ->withCount('Post.products')
            ->whereHas('Post', function ($where_has_posts) {
                return $where_has_posts->whereNull('post_type');
            })
            ->whereHas('Post.business')
            ->relatedToUser();
        if ($keyword) {
            $invitations = $invitations->whereHas('Post', function ($q) {
                return $q->where('post_name', 'like', '%' . request('keyword') . '%');
            });
        }
        if ($status) {
            $statuses = collect(request('status'))->map(function ($statusString) {
                return PostUser::getStatusNumber($statusString);
            })->toArray();
            $invitations = $invitations->whereIn('status', $statuses);
        }
        return $invitations;
    }

    public function getInvitationToShow($invitation_id)
    {
        return PostUser::select('id', 'user_id', 'user_type', 'status', 'created_at', 'post_id', 'reason', 'reject_amount')
            ->with([
                'Post' => function ($query) {
                    $query->select('id', 'post_name', 'price_per_post', 'description', 'hashtag')
                        ->with('products:id,user_id,name,sku,brand,status,price,retail_price,discount');
                },
                'influencerPriceBase' => function ($query) {
                    return $query->select('id', 'post_id', 'user_id', 'infulancer_id', 'price', 'final_price');
                },
                'whatsAppGroup',
                'whatsAppGroup.city:id,name,country_id',
                'whatsAppGroup.city.country:id,name'
            ])
            ->relatedToUser()
            ->where('id', $invitation_id)
            ->firstOrFail();
    }

    public function applyInvitation($data)
    {
        $invitation = PostUser::relatedToUser()
            ->where('id', $data['invitation_id'])
            ->where('status', PostUser::PENDING_STATUS)
            ->firstOrFail();
        $invitation->update(
            [
                'reason' => $data['application'],
                'reject_amount' => $data['influencer_amount'] ?? null,
                'status' => PostUser::AppliedStatus,
            ]
        );
        if (isset($data['influencer_amount'])) {
            $invitation->influencerPriceObject()->update(['final_price' => $data['influencer_amount']]);
        }

        return $invitation;
    }

    public function applyInvitationBroadcast($data)
    {
        $invitation = PostUser::create([
            'user_id' => auth('api')->id(),
            'post_id' => $data['post_id'],
            'status' => PostUser::AppliedStatus,
            'reason' => $data['application'],
            'reject_amount' => $data['influencer_amount'] ?? null,
            'is_submit' => 1,
            'is_resend' => 0
        ]);
        PostPrice::create(
            [
                'user_id' => $invitation->post->business()->withTrashed()->first()->id ?? 0,
                'infulancer_id' => auth('api')->id(),
                'post_id' => $data['post_id'],
                'status' => 1,
                'price' => $invitation->post->price_per_post,
                'complementry_item' => null,
                'is_resend' => 0,
                'final_price' => $data['influencer_amount'] ?? $invitation->post->price_per_post,
            ]
        );
        return $invitation;
    }

    public function declineInvitation($data)
    {
        $invitation = PostUser::relatedToUser()
            ->where('id', $data['invitation_id'])
            ->where('status', PostUser::PENDING_STATUS)
            ->firstOrFail();
        $invitation->update(
            [
                'status' => PostUser::DeclinedStatus,
            ]
        );
        return $invitation;
    }

    public function getInvitationsToIndexBroadcast($keyword = null, $status = null)
    {
        $invitations = Post::where('post_type', Post::BROADCAST)
            ->where('approve_status', ApproveStatus::Approved->value)
            //            ->select('id', 'post_name', 'price_per_post')
            ->with(['business' => function ($businessQuery) {
                $businessQuery->select('id', 'name');
            }, 'influencersInPost'])
            ->withCount('products')
            ->latest();

        if ($keyword) {
            $invitations = $invitations->where('post_name', 'like', '%' . request('keyword') . '%');
        }
        if ($status) {
            $statuses = collect(request('status'))->map(function ($statusString) {
                return PostUser::getStatusNumber($statusString);
            })->toArray();
            $invitations = $invitations->whereHas('influencersInPost', function ($influencersInPost) use ($statuses) {
                $influencersInPost->whereIn('status', $statuses);
            });
        }
        return $invitations;
    }

    public function getInvitationToShowBroadcast($invitation_id)
    {
        return Post::with([
            'influencersInPost' => function ($influencersInPost) {
                $influencersInPost->select('id', 'user_id', 'status', 'created_at', 'post_id', 'reason', 'reject_amount');
            },
            'products:id,user_id,name,sku,brand,status,price,retail_price,discount'
        ])
            ->where('post_type', Post::BROADCAST)
            ->where('id', $invitation_id)
            ->firstOrFail();
    }
}
