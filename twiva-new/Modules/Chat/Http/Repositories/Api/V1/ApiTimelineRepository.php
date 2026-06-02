<?php

namespace Modules\Chat\Http\Repositories\Api\V1;

use Modules\Chat\Entities\ChatGroup;
use Modules\Chat\Entities\Comment;
use Modules\Chat\Entities\Timeline;
use Modules\Chat\Jobs\TimelineNotifyCommentPostByEmailAndFcmJob;
use Modules\Chat\Jobs\TimelineNotifyLikePostByEmailAndFcmJob;

class ApiTimelineRepository {
    public function __construct()
    {
    }


    public function listOfTimelines($filters)
    {
        $timelines_query = Timeline::query()
            ->withCount('views', 'likes', 'comments')
            ->whereHas('user', function ($where_has_query)
            {
                return $where_has_query
                    ->whereNull('banned_at')
                    ->whereNull('de_active_community_at');
            })
            ->with('likes');
        if (array_key_exists('chat_group_id', $filters) && $filters['chat_group_id'])
        {
            $timelines_query->where('chat_group_id', $filters['chat_group_id']);
        }

        if (array_key_exists('timeline_id', $filters) && $filters['timeline_id'])
        {
            $timelines_query->where('id', $filters['timeline_id']);
        }
        else
        {
            $timelines_query->whereNull('chat_group_id');
        }
        if ($filters['order_by'] ?? false)
        {
            $timelines_query->orderByDesc($filters['order_by'])->latest();
        }
        else
        {
            $timelines_query->latest();
        }

        return $timelines_query->paginate($filters['per_page']);
    }

    public function pinned()
    {
        $chat_group_id = request('chat_group_id');

        $timelines_query = Timeline::query()
            ->where('is_pinned', 1)
            ->withCount('views', 'likes', 'comments')
            ->latest();

        if ($chat_group_id)
        {
            $timelines_query->where('chat_group_id', $chat_group_id);
        }


        return $timelines_query->limit(1)->get();
    }

    public function create($data)
    {

        $data['user_id'] = auth('api')->id();
        $put_in = Timeline::pathTimeline();
        if (array_key_exists('image', $data))
        {
            $data['image'] = uploadFile($data['image'], $put_in);
        }
        else
        {
            $url = extractFirstUrl($data['content']);
            if ($url)
            {
                $response = generateThumbnail($url);
                if ($response && $response->getStatusCode() == 200)
                {
                    $data['image'] = uploadBinaryFile($response->getBody()->getContents(), $put_in);
                }
            }
            else
            {
            }
        }

        $timeline = Timeline::create($data);

        return $timeline;
    }

    public function listOfCommentsOfTimeline($filters, Timeline $timeline)
    {
        $timeline_comments_query = Comment::where('timeline_id', $timeline->id)
            ->whereNull('reply_id')
            ->with('user')
            ->withCount(['comments'])
            ->latest();

        return $timeline_comments_query->paginate($filters['per_page']);
    }

    public function listOfReplaysForComments($filters, Timeline $timeline, $commentId)
    {
        $timeline_comments_query = Comment::where('timeline_id', $timeline->id)
            ->where('reply_id', $commentId)
            ->with('user')
            ->withCount(['comments'])
            ->latest();
        return $timeline_comments_query->paginate($filters['per_page']);
    }

    public function view($timeline)
    {
        $view = $timeline->views()->where('user_id', auth('api')->id())->first();
        if (!$view)
        {
            $view = $timeline->views()->create(['user_id' => auth('api')->id()]);
        }
        return $view;
    }

    public function like(TimeLine $timeline)
    {
        $user = auth('api')->user();
        $like = $timeline->likes()->where('user_id', $user->id)->first();
        if ($like)
        {
            $like->delete();
        }
        else
        {
            $like = $timeline->likes()->create(['user_id' => $user->id]);
            TimelineNotifyLikePostByEmailAndFcmJob::dispatch($timeline, $user);
        }
        return $like;
    }

    public function comment($data, $timeline)
    {
        $user = auth('api')->user();
        $data['user_id'] = $user->id;
        $comment = $timeline->comments()->create($data);
        TimelineNotifyCommentPostByEmailAndFcmJob::dispatch($timeline, $user);
        return $comment;
    }

    public function listOfChatGroups($filters)
    {
        $groups_query = ChatGroup::query()
            ->withCount('users')
            ->latest();

        if (array_key_exists('chat_group_id', $filters) && $filters['chat_group_id'])
        {
            $groups_query->where('id', $filters['chat_group_id']);
        }

        return $groups_query->paginate($filters['per_page']);
    }

    public function delete($timeline)
    {
        if (is_null($timeline->deleted_at))
        {
            $result = $timeline->delete();
        }
        else
        {
            $result = $timeline->restore();
        }
        return $result;
    }

    public function pin($timeline)
    {
        $chat_group_id = $timeline->chat_group_id;
        if ($chat_group_id)
        {
            Timeline::where('chat_group_id', $chat_group_id)->update(['is_pinned' => 0]);
            Timeline::where('chat_group_id', $chat_group_id)->where('id', $timeline->id)->update(['is_pinned' => 1]);
        }
        else
        {
            Timeline::whereNull('chat_group_id')->update(['is_pinned' => 0]);
            Timeline::whereNull('chat_group_id')->where('id', $timeline->id)->update(['is_pinned' => 1]);
        }

        return true;
    }
}
