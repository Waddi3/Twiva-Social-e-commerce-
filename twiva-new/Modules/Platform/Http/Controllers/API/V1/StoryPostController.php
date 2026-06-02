<?php


namespace Modules\Platform\Http\Controllers\API\V1;


use Illuminate\Support\Facades\Auth;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\StoryPost;
use Modules\Platform\Http\Requests\CreateStoryPostRequest;
use Modules\Platform\Http\Requests\IndexStoryPostRequest;
use Modules\Platform\Http\Resources\IndexStoryPostCollection;
use Modules\Platform\Http\Resources\ShowStoryPostResourceCollection;
use Modules\Platform\Repositories\StoryPostRepository;

class StoryPostController
{
    public function index(IndexStoryPostRequest $request)
    {
        $userId = Auth::id();
        $isSchedule = $request->validated()['is_schedule'] ?? 0;
        if ($isSchedule)
        {
            $storyList = StoryPost::with('productDetails')
                ->whereHas('productDetails')
                ->where('influencer_id', $userId)
                ->where('is_completed', 0)
                ->whereNull('canceled_at')
                ->orderBy('created_at', 'DESC');
        }
        else  //history
        {
            $storyList = StoryPost::with('productDetails')
                ->whereHas('productDetails')
                ->where('influencer_id', $userId)
                ->where(function ($q) {
                    $q->where('is_completed', 1)->orWhereNotNull('canceled_at');
                })->orderBy('created_at', 'DESC');
        }

        return response()->json(['data' => IndexStoryPostCollection::collection($storyList->paginate(10))->collection]);
    }

    public function create(CreateStoryPostRequest $request)
    {
        $data = $request->validated();
        $userPlatforms = Auth::guard('api')->user()->UserPlatforms()->pluck('platform_id')->toArray();

        if (array_diff(explode(',', $data['platform']), $userPlatforms))
        {
            return response()->json(['message' => 'Please connect the selected platforms first'], 403);
        }
        $story = StoryPostRepository::StoreStoryPost($data);
        // has error
        if (method_exists($story, 'getData'))
        {
            return $story;
        }
        StoryPostRepository::handlePostToSocialMedia($story);
        return response()->json(['message' => "Posted story saved successfully"]);
    }

    public function show()
    {
        $story = StoryPost::where('id', request('story_id'))
            ->where('influencer_id', \auth('api')->id())
            ->whereHas('productDetails')
            ->firstOrFail();
        return response()->json(['data' => new ShowStoryPostResourceCollection($story)]);
    }

    public function cancel()
    {
        $story = StoryPost::where('id', request('story_id'))
            ->where('influencer_id', \auth('api')->id())
            ->where('is_completed', 0)
            ->where('is_schedule', 1)
            ->first();
        if ($story)
        {
            $story->update(['canceled_at' => now()]);
            return response()->json(['message' => 'Story post has been canceled']);
        }
        return response()->json(['message' => 'This action is not authorized'], 403);

    }


}
