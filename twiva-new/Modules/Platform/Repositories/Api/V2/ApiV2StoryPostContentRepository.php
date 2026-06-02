<?php


namespace Modules\Platform\Repositories\Api\V2;

use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\User\Entities\User;

class ApiV2StoryPostContentRepository {
    public static function generateVideo($product_id)
    {
        ini_set('max_execution_time', 600);
        $user = auth('api')->user();

        $video = '';
        $video_with_subtitle = '';

        [$product, $_] = Product::product($user, $product_id, false);
        if ($product)
        {
            if (!$product->video?->video)
            {
                $product_images = $product->productImages;

                if (!count($product_images))
                {
                    $product_images = $product->BusinessProductImages;
                }

                $images = $product_images->pluck('image');
                $images = Product::resizeImageForVideo($images);

                // $images = $images->implode(' '); // image-01.jpg image-02.jpg image-03.jpg
                $buy_link = route('shop.product.show', ['account' => $user->shop->domain, 'product' => $product->id]);

                $video = generateVideo($images, $buy_link, false, false, $product);
                $video_with_subtitle = generateVideo($images, $buy_link, true, false, $product);
            }
            else
            {
                $video = $product->video?->video_url;
                $video_with_subtitle = $product->video?->video_with_subtitle_url;
            }
        }


        return ['vedeo' => $video, 'video_with_subtitle' => $video_with_subtitle];
    }
}
