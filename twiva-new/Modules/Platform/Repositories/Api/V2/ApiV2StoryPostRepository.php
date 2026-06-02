<?php


namespace Modules\Platform\Repositories\Api\V2;


use Illuminate\Support\Facades\DB;
use Modules\Influencer\Entities\Product;
use Modules\Platform\Entities\StoryPost;
use Modules\User\Entities\User;

class ApiV2StoryPostRepository
{
    public static function index($filters)
    {
        $user = auth('api')->user();
        $user_id = $user->id;
        $user_type = $user->type;
        $product_details = $user_type == User::InfluencerType ? ':id,product_title' : ':id,name';
        $story_posts_query = StoryPost::query()
            ->select('*')
            ->with('productDetails' . $product_details)
            ->whereHas('productDetails')
            ->where('influencer_id', $user_id)
            ->addSelect(
                DB::raw("(SELECT JSON_ARRAYAGG(JSON_OBJECT('id', platforms.id, 'name', platforms.name)) FROM platforms WHERE FIND_IN_SET(platforms.id, user_story_post.platform)) AS platforms")
            )
            ->orderBy('created_at', 'DESC');
        if (array_key_exists('type', $filters) && $filters['type'] == 'scheduled') {
            $story_posts_query->where('is_completed', 0);
        } elseif (array_key_exists('type', $filters) && $filters['type'] == 'posted') {
            $story_posts_query->where('is_completed', 1);
        } else { // all
        }
        return $story_posts_query->paginate($filters['per_page']);
    }

    public function create($data, $user)
    {

        $data['influencer_id'] = $user->id;
        $data['platform'] = collect($data['platform_ids'])->implode(',');
        unset($data['platform_ids']);

        [$product, $_] = Product::product($user, $data['product_id'], false);

        $product_images = $product->productImages;

        if (!count($product_images)) {
            $product_images = $product->BusinessProductImages;
        }

        $images = $product_images->pluck('image');

        $data['image'] = Product::resizeImageForVideo($images, ',');

        $data['is_completed'] = 0;

        if (isset($data['publish_type'])) {
            $data['publish_type'] = [
                'post_type' => $data['publish_type'],
            ];
        } else {
            $data['publish_type'] = [
                'post_type' => null,
            ];
        }

        if (
            isset($data['tiktok_privacy'])
            || isset($data['tiktok_allow_user_to_comment'])
            || isset($data['tiktok_allow_user_to_duet'])
            || isset($data['tiktok_allow_user_to_stitch'])
            || isset($data['tiktok_your_brand'])
            || isset($data['tiktok_branded_content'])
        ) {
            $data['publish_type'] = array_merge(
                $data['publish_type'],
                [
                    'tiktok' =>
                    array_filter(
                        [
                            'tiktok_privacy' => $data['tiktok_privacy'] ?? null,
                            'tiktok_allow_user_to_comment' => isset($data['tiktok_allow_user_to_comment']) ? $data['tiktok_allow_user_to_comment'] : null,
                            'tiktok_allow_user_to_duet' => isset($data['tiktok_allow_user_to_duet']) ? $data['tiktok_allow_user_to_duet'] : null,
                            'tiktok_allow_user_to_stitch' => isset($data['tiktok_allow_user_to_stitch']) ? $data['tiktok_allow_user_to_stitch'] : null,
                            'tiktok_your_brand' => isset($data['tiktok_your_brand']) ? $data['tiktok_your_brand'] : null,
                            'tiktok_branded_content' => isset($data['tiktok_branded_content']) ? $data['tiktok_branded_content'] : null,
                        ],
                        fn($value) =>  !is_null($value)
                    )
                ]
            );
            unset($data['tiktok_privacy']);
            unset($data['tiktok_allow_user_to_comment']);
            unset($data['tiktok_allow_user_to_duet']);
            unset($data['tiktok_allow_user_to_stitch']);
            unset($data['tiktok_your_brand']);
            unset($data['tiktok_branded_content']);
        }


        $story_user_post = StoryPost::query()->create($data);
        return $story_user_post;
    }
}
