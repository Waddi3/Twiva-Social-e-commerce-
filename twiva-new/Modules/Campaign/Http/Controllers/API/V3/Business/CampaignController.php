<?php

namespace Modules\Campaign\Http\Controllers\API\V3\Business;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Campaign\Entities\Post;
use Modules\Campaign\Entities\PostPrice;
use Modules\Campaign\Entities\PostUser;
use Modules\Campaign\Http\Requests\Api\ApiWaivePromptRequest;
use Modules\Campaign\Http\Requests\HireCampaignRequest;
use Modules\Campaign\Http\Requests\CancelCampaignRequest;
use Modules\Campaign\Http\Requests\CreateCampaignRequest;
use Modules\Campaign\Http\Resources\Business\IndexCampaignCollection;
use Modules\Campaign\Http\Resources\Business\ShowCampaignCollection;
use Modules\Campaign\Repository\Business\CampaignRepository;
use Modules\Home\Http\Controllers\API\V2\BaseTrait;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;
use function request;

class CampaignController extends Controller
{
    use BaseTrait;

    private $campaignRepository;

    public function __construct(CampaignRepository $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }


    public function index(): JsonResponse
    {
        // dd(auth('api')->id());
        $posts = Post::with(["user", "Images", 'influencersInPost'])
            ->select("posts.*")
            ->withCount('products')
            ->orderBy("posts.id", "desc")
            ->where('posts.user_id', auth('api')->id());
        if (request('keyword')) {
            $posts = $posts->where('post_name', 'LIKE', '%' . request('keyword') . '%');
        }

        $broadcast_filter = filter_var(request('broadcast'), FILTER_VALIDATE_BOOLEAN);
        if ($broadcast_filter) {
            $posts->where('post_type', Post::BROADCAST);
        } else {
            $posts->whereHas('influencersInPost');
            $posts->whereHas('influencersInPost.Post', function ($where_has_posts) {
                return $where_has_posts->whereNull('post_type');
            });
        }

        return response()->json(['data' => IndexCampaignCollection::collection($posts->paginate(10))->resource, 'status' => 200]);
    }

    public function show(): JsonResponse
    {
        $post = Post::whereId(request('post_id'))
            ->where('posts.user_id', auth('api')->id())
            ->with(["Images", 'influencersInPost', 'products:id,user_id,name,sku,brand,status,price,retail_price,discount'])
            ->firstOrFail();
        return response()->json(['data' => new ShowCampaignCollection($post), 'status' => 200]);
    }

    public function create(CreateCampaignRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $post_id = $this->campaignRepository->create($validated);
        return response()->json(["message" => "Post created successfully.", 'post_id' => $post_id, 'status' => 200]);
    }

    public function hire(HireCampaignRequest $request)
    {
        $post = Post::find($request->post_id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ]);
        }

        try {
            foreach ($request->influencer_ids as $influencer_id) {
                if (
                    !PostUser::where('user_id', $influencer_id)
                        ->where('user_type', $post->user_type)
                        ->where('post_id', $request->post_id)
                        ->count()
                ) {
                    $postUser = PostUser::create([
                        'user_id' => $influencer_id,
                        'user_type' => $post->user_type,
                        'post_id' => $request->post_id,
                        'status' => PostUser::PENDING_STATUS,
                        'is_submit' => 1,
                        'reason' => $request->input('reason')
                    ]);

                    if ($postUser) {
                        PostPrice::create([
                            'user_id' => $post->business->id,
                            'infulancer_id' => $influencer_id,
                            'user_type' => $post->user_type,
                            'post_id' => $post->id,
                            'status' => 1,
                            'price' => $post->price_per_post,
                            'complementry_item' => null,
                            'is_resend' => 0,
                            'final_price' => $post->price_per_post,
                        ]);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Influencers are hired for the campaign'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while hiring influencer',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function cancel(CancelCampaignRequest $request)
    {
        $post = Post::find($request->post_id);

        $postUser = PostUser::where('user_id', $request->influencer_id)
            ->where('user_type', $post->user_type)
            ->where('post_id', $request->post_id)
            ->first();

        if (!$postUser) {
            return response()->json([
                'status' => false,
                'message' => 'The influencer has not applied for the campaign'
            ]);
        }

        try {

            /*if( Carbon::now()->diff( $postUser->created_at )->format('%d') >= 1 ) {
                return response()->json([
                    'status' => false,
                    'message' => 'Campaign subscription cannot be canceled after 24 hours'
                ]);
            }*/

            // Remove PostUser and PostPrice
            $postUser->influencerPriceBase()
                ->where('infulancer_id', $request->influencer_id)
                ->where('user_type', $post->user_type)
                ->where('post_id', $request->post_id)
                ->delete();
            $postUser->delete();

            return response()->json([
                'status' => true,
                'message' => 'The post has deleted successfully'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while cancelling the post',
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function checkWaivePrompt(ApiWaivePromptRequest $request)
    {
        $user = auth('api')->user();
        $validated = $request->validated();

        if ($user) {
            if ($user->type == User::BusinessType) {
                if ($user->waive_prompt) {
                    CampaignRepository::payForCampaign($validated['post_id']);
                    return response()->json(['data' => ['waive_prompt' => $user->waive_prompt, 'message' => 'Influencers Hired']]);
                } else {
                    return response()->json(['data' => ['waive_prompt' => $user->waive_prompt, 'message' => 'Business has to pay']]);
                }
            }
        }
        return response()->json([
            "message" => "Please check your data",
            "errors" => [
                "data" => [
                    "Please check your data"
                ]
            ]
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
