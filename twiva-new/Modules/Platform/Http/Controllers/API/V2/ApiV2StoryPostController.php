<?php


namespace Modules\Platform\Http\Controllers\API\V2;


use Modules\Platform\Entities\StoryPost;
use Modules\Platform\Http\Requests\Api\V2\ApiV2StoryPostCreateRequest;
use Modules\Platform\Http\Requests\Api\V2\ApiV2StoryPostIndexRequest;
use Modules\Platform\Http\Resources\ShowStoryPostResourceCollection;
use Modules\Platform\Repositories\Api\V2\ApiV2StoryPostRepository;
use Modules\Platform\Services\V2\RunScheduleSocialMedia;
use Modules\Platform\Transformers\Api\V2\ApiV2IndexStoryPostCollection;
use Symfony\Component\HttpFoundation\Response;
use function auth;

class ApiV2StoryPostController
{

    public function __construct(public ApiV2StoryPostRepository $api_v2_story_post_repository) {}
    public function index(ApiV2StoryPostIndexRequest $request)
    {
        $filters = $request->validated();

        $story_posts = $this->api_v2_story_post_repository->index($filters);

        // dd(auth('api')->id());

        return response()->json(['data' => ApiV2IndexStoryPostCollection::collection($story_posts)->collection], Response::HTTP_OK);
    }

    public function create(ApiV2StoryPostCreateRequest $request)
    {
        $user = auth('api')->user();
        $data = $request->validated();
        $story_post = $this->api_v2_story_post_repository->create($data, $user);
        if(!$story_post->is_schedule){
            new RunScheduleSocialMedia($story_post);
        }
        return response()->json(['data' => new ApiV2IndexStoryPostCollection($story_post)], Response::HTTP_CREATED);
    }

    public function show()
    {
        $story = StoryPost::where('id', request('story_id'))
            ->where('influencer_id', auth('api')->id())
            ->whereHas('productDetails')
            ->firstOrFail();
        return response()->json(['data' => new ShowStoryPostResourceCollection($story)]);
    }

    public function cancel()
    {
        $story = StoryPost::where('id', request('story_id'))
            ->where('influencer_id', auth('api')->id())
            ->where('is_completed', 0)
            ->where('is_schedule', 1)
            ->first();
        if ($story) {
            $story->update(['canceled_at' => now()]);
            return response()->json(['message' => 'Story post has been canceled']);
        }
        return response()->json(['message' => 'This action is not authorized'], 403);
    }
}
