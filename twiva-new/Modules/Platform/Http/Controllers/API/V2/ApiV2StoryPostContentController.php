<?php


namespace Modules\Platform\Http\Controllers\API\V2;

use Modules\Platform\Repositories\Api\V2\ApiV2StoryPostContentRepository;
use Modules\Platform\Transformers\Api\V2\ApiV2IndexStoryPostCollection;
use Symfony\Component\HttpFoundation\Response;

class ApiV2StoryPostContentController
{

    public function __construct(public ApiV2StoryPostContentRepository $api_v2_story_post_content_repository) {}
    public function generateVideo($product_id)
    {
        $videos_url = $this->api_v2_story_post_content_repository->generateVideo($product_id);

        return response()->json(['data' => [$videos_url]], Response::HTTP_OK);
    }
}
