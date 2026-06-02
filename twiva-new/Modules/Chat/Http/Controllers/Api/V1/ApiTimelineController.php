<?php

namespace Modules\Chat\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller;
use Modules\Chat\Entities\Timeline;
use Modules\Chat\Http\Repositories\Api\V1\ApiTimelineRepository;
use Modules\Chat\Http\Requests\Api\V1\ApiTimelineAddCommentsRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiTimelineCreateRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiTimelineListCommentsRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiTimelineListRequest;
use Modules\Chat\Jobs\ViewedListTimelineJob;
use Modules\Chat\Transformers\Api\V1\ApiListCommentsOfTimelineResource;
use Modules\Chat\Transformers\Api\V1\ApiListTimelineResource;
use Symfony\Component\HttpFoundation\Response;

class ApiTimelineController extends Controller
{

    public function __construct(public ApiTimelineRepository $api_timeline_repository) {}

    public function index(ApiTimelineListRequest $request)
    {
        $filters = $request->validated();
        $filters['order_by'] = 'is_pinned';
        $timelines = $this->api_timeline_repository->listOfTimelines($filters);

        $user_id = auth('api')->id();
        ViewedListTimelineJob::dispatch($timelines, $user_id);

        return response()->json(ApiListTimelineResource::collection($timelines)->resource, Response::HTTP_OK);
    }

    public function pinned()
    {
        $pinned_timeline = $this->api_timeline_repository->pinned();
        return response()->json(ApiListTimelineResource::collection($pinned_timeline), Response::HTTP_OK);
    }

    public function create(ApiTimelineCreateRequest $request)
    {

        $data = $request->validated();
        $timeline = $this->api_timeline_repository->create($data);
        // TimelineNotifyByEmailAndFcmJob::dispatch($timeline);
        return response()->json(['data' => ['timeline' => ['id' => $timeline->id]]], Response::HTTP_OK);
    }

    public function show($timeline)
    {
        if (!$timeline)
        {
            return response()->json(['data' => ['error' => 'data not found']], Response::HTTP_NOT_FOUND);
        }
        $filters['timeline_id'] = $timeline;
        $filters['per_page'] = 1;
        $timelines = $this->api_timeline_repository->listOfTimelines($filters);
        return response()->json(ApiListTimelineResource::collection($timelines), Response::HTTP_OK);
    }

    public function view($timeline)
    {
        $timeline = Timeline::find($timeline);
        if (!$timeline)
        {
            return response()->json(['data' => ['error' => 'data not found']], Response::HTTP_NOT_FOUND);
        }
        $timeline_view = $this->api_timeline_repository->view($timeline);
        return response()->json(['data' => [true]], Response::HTTP_OK);
    }

    public function like($timeline)
    {
        $timeline = Timeline::find($timeline);
        if (!$timeline)
        {
            return response()->json(['data' => ['error' => 'data not found']], Response::HTTP_NOT_FOUND);
        }
        $timeline_like = $this->api_timeline_repository->like($timeline);
        return response()->json(['data' => [true]], Response::HTTP_OK);
    }

    public function comment(ApiTimelineAddCommentsRequest $request, $timeline)
    {
        $timeline = Timeline::find($timeline);
        if (!$timeline)
        {
            return response()->json(['data' => ['error' => 'data not found']], Response::HTTP_NOT_FOUND);
        }
        $data = $request->validated();
        $timeline_comment = $this->api_timeline_repository->comment($data, $timeline);
        return response()->json(['data' => ['comment' => ['id' => $timeline_comment->id]]], Response::HTTP_OK);
    }

    public function comments(ApiTimelineListCommentsRequest $request, $timeline)
    {
        $timeline = Timeline::find($timeline);
        if (!$timeline)
        {
            return response()->json(['data' => ['error' => 'data not found']], Response::HTTP_NOT_FOUND);
        }
        $filters = $request->validated();
        $timeline_comments = $this->api_timeline_repository->listOfCommentsOfTimeline($filters, $timeline);
        return response()->json(ApiListCommentsOfTimelineResource::collection($timeline_comments)->resource, Response::HTTP_OK);
    }

    public function commentReplies(ApiTimelineListCommentsRequest $request, $timeline, $replyId)
    {
        $timeline = Timeline::find($timeline);
        if (!$timeline)
        {
            return response()->json(['data' => ['error' => 'data not found']], Response::HTTP_NOT_FOUND);
        }
        $filters = $request->validated();
        $commentReplies = $this->api_timeline_repository->listOfReplaysForComments($filters, $timeline, $replyId);
        return response()->json(ApiListCommentsOfTimelineResource::collection($commentReplies)->resource, Response::HTTP_OK);
    }

    public function delete($timeline)
    {
        $timeline = Timeline::withTrashed()->find($timeline);
        if (!$timeline)
        {
            return response()->json(['data' => ['error' => 'data not found']], Response::HTTP_NOT_FOUND);
        }
        $timeline = $this->api_timeline_repository->delete($timeline);
        return response()->json(['data' => [$timeline]], Response::HTTP_OK);
    }

    public function pin($timeline)
    {
        $timeline = Timeline::withTrashed()->find($timeline);
        if (!$timeline)
        {
            return response()->json(['data' => ['error' => 'data not found']], Response::HTTP_NOT_FOUND);
        }
        $timeline = $this->api_timeline_repository->pin($timeline);
        return response()->json(['data' => [$timeline]], Response::HTTP_OK);
    }
}
