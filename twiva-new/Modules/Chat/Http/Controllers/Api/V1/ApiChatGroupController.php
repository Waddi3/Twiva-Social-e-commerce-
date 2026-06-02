<?php

namespace Modules\Chat\Http\Controllers\Api\V1;

use Illuminate\Routing\Controller;
use Modules\Chat\Http\Repositories\Api\V1\ApiChatGroupRepository;
use Modules\Chat\Http\Requests\Api\V1\ApiChatGroupAddMembersRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiChatGroupCreateRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiChatGroupListOfMembersRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiChatGroupListRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiChatGroupUpdateRequest;
use Modules\Chat\Http\Requests\Api\V1\ApiChatListOfUserRequest;
use Modules\Chat\Jobs\ViewedListTimelineJob;
use Modules\Chat\Transformers\Api\V1\ApiChatGroupUserLisResource;
use Modules\Chat\Transformers\Api\V1\ApiGroupTimelineListResource;
use Modules\Chat\Transformers\Api\V1\ApiListChatGroupMemberResource;
use Modules\Chat\Transformers\Api\V1\ApiListChatGroupOfTimelineResource;
use Symfony\Component\HttpFoundation\Response;

class ApiChatGroupController extends Controller
{

    public function __construct(public ApiChatGroupRepository $api_chat_group_repository) {}

    public function groups(ApiChatGroupListRequest $request)
    {
        $filters = $request->validated();
        $groups = $this->api_chat_group_repository->listOfChatGroups($filters);
        return response()->json(ApiListChatGroupOfTimelineResource::collection($groups)->resource, Response::HTTP_OK);
    }

    public function show(ApiChatGroupListRequest $request, $group)
    {
        $filters = $request->validated();
        $filters['chat_group_id'] = $group;
        $groups = $this->api_chat_group_repository->listOfChatGroups($filters);
        return response()->json(ApiListChatGroupOfTimelineResource::collection($groups), Response::HTTP_OK);
    }

    public function getGroupPosts(ApiChatGroupListRequest $request, $group)
    {
        $filters = $request->validated();
        $filters['chat_group_id'] = $group;
        $groupPosts = $this->api_chat_group_repository->getGroupTimeline($filters);
        $user_id = auth('api')->id();
        ViewedListTimelineJob::dispatch($groupPosts, $user_id);
        return response()->json(['posts' => ApiGroupTimelineListResource::collection($groupPosts)], Response::HTTP_OK);
    }

    public function create(ApiChatGroupCreateRequest $request)
    {

        $data = $request->validated();
        $chat_group = $this->api_chat_group_repository->create($data);
        return response()->json(['data' => ['chat_group' => ['id' => $chat_group->id]]], Response::HTTP_OK);
    }

    public function update(ApiChatGroupUpdateRequest $request, $group_id)
    {
        $data = $request->validated();
        $chat_group = $this->api_chat_group_repository->update($data, $group_id);
        return response()->json(['data' => ['msg' => $chat_group ? 'Updated' : 'Not Update!']], $chat_group ? Response::HTTP_OK: Response::HTTP_BAD_REQUEST);
    }

    public function delete($group_id)
    {
        $chat_group = $this->api_chat_group_repository->delete($group_id);
        return response()->json(['data' => ['msg' => $chat_group ? 'deleted' : 'Not deleted!']], $chat_group ? Response::HTTP_OK: Response::HTTP_BAD_REQUEST);
    }

    public function members(ApiChatGroupListOfMembersRequest $request, $group)
    {
        $filters = $request->validated();
        $members = $this->api_chat_group_repository->listOfMembersChatGroups($filters, $group);
        return response()->json(ApiListChatGroupMemberResource::collection($members)->resource, Response::HTTP_OK);
    }

    public function syncMembers(ApiChatGroupAddMembersRequest $request)
    {
        $data = $request->validated();
        $result = $this->api_chat_group_repository->syncMembers($data);
        return response()->json(['data' => ['result' => $result]], Response::HTTP_OK);
    }

    public function users(ApiChatListOfUserRequest $request)
    {
        $filters = $request->validated();
        $users = $this->api_chat_group_repository->users($filters);
        return response()->json(ApiChatGroupUserLisResource::collection($users)->resource, Response::HTTP_OK);
    }

    public function usersBanned(ApiChatListOfUserRequest $request)
    {
        $filters = $request->validated();
        $users = $this->api_chat_group_repository->usersBanned($filters);
        return response()->json(ApiChatGroupUserLisResource::collection($users)->resource, Response::HTTP_OK);
    }

    public function userBanned($user)
    {
        $result = $this->api_chat_group_repository->userBanned($user);
        return response()->json(['data' => ['user_banned' => [$result]]], Response::HTTP_OK);
    }
}
