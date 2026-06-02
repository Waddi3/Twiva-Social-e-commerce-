<?php


namespace Modules\Platform\Repositories;


use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\Notification\Notifications\Influencer\PostIsScheduledNotification;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\StoryPost;
use Modules\Platform\Jobs\PostToFacebookJob;
use Modules\Platform\Jobs\PostToInstagramJob;
use Modules\Platform\Jobs\PostToTiktokJob;
use Modules\Platform\Jobs\PostToTwitterJob;
use function auth;

class StoryPostRepository {
    public static function StoreStoryPost($data)
    {
        if ($data['is_schedule'] == 1)
        {
            if ($data['repeat_type'] && $data['repeat_type'] == StoryPost::REPEAT_TYPE_IN_DAYS && !isset($data['repeated_days']))
            {
                return response()->json(['success' => false, 'message' => 'Repeated days is required'], 400);
            }

            if ($data['end_after_type'] && $data['end_after_type'] == StoryPost::END_AFTER_TYPE_AFTER_DATE && !isset($data['end_date']))
            {
                return response()->json(['success' => false, 'message' => 'End Date is required'], 400);
            }

            if ($data['end_after_type'] && $data['end_after_type'] == StoryPost::END_AFTER_TYPE_AFTER_COUNT_OF_TIMES && !isset($data['number_of_time']))
            {
                return response()->json(['success' => false, 'message' => 'Number of time post share is required'], 400);
            }
            $data['is_completed'] = 0;
        }

        $data['influencer_id'] = auth('api')->id();
        $data['image'] = self::saveImages($data);
        unset($data['images_from_product']);
        if (array_key_exists('number_of_time', $data) and is_null($data['number_of_time']))
        {
            $data['number_of_time'] = 0;
        }
        $storyPost = StoryPost::create($data);
        return $storyPost;
    }

    public static function handlePostToSocialMedia(StoryPost $story)
    {
        if ($story->is_schedule == 1)
        {
            $postDates = self::generatePostDate($story);
            foreach ($postDates as $postDate)
            {
                if ($story->hasPlatform(Platform::FacebookID))
                {
                    PostToFacebookJob::dispatch(auth('api')->id(), $story->id)->delay($postDate);
                }
                if ($story->hasPlatform(Platform::InstagramID))
                {
                    PostToInstagramJob::dispatch(auth('api')->id(), $story->id)->delay($postDate);
                }
                if ($story->hasPlatform(Platform::TwitterID))
                {
                    PostToTwitterJob::dispatch(auth('api')->id(), $story->id)->delay($postDate);
                }
                if ($story->hasPlatform(Platform::TikTokID))
                {
                    PostToTiktokJob::dispatch(auth('api')->id(), $story->id)->delay($postDate);
                }
            }
            auth('api')->user()->notify(new PostIsScheduledNotification($story));
        }
        else
        {
            if ($story->hasPlatform(Platform::FacebookID))
            {
                PostToFacebookJob::dispatch(auth('api')->id(), $story->id);
            }
            if ($story->hasPlatform(Platform::InstagramID))
            {
                PostToInstagramJob::dispatch(auth('api')->id(), $story->id);
            }
            if ($story->hasPlatform(Platform::TwitterID))
            {
                PostToTwitterJob::dispatch(auth('api')->id(), $story->id);
            }
            if ($story->hasPlatform(Platform::TikTokID))
            {
                PostToTiktokJob::dispatch(auth('api')->id(), $story->id);
            }
        }
    }

    public static function generatePostDate($story)
    {
        $dates = [];

        $endDate = Carbon::parse($story->end_date);
        $startDate = Carbon::parse($story->start_date);
        if ($story->end_after_type == StoryPost::END_AFTER_TYPE_AFTER_COUNT_OF_TIMES)
        {
            $endDate = $startDate->copy()->addDays($story->number_of_time - 1);
        }
        if ($story->repeat_type == StoryPost::REPEAT_TYPE_DAILY)
        {
            if ($endDate == $startDate)
            {
                $dates[] = $endDate;
            }
            else
            {
                $dateRanges = CarbonPeriod::create($startDate, $endDate);
                foreach ($dateRanges as $date)
                {
                    $dates[] = Carbon::parse($date);
                }
            }
        }
        else if ($story->repeat_type == StoryPost::REPEAT_TYPE_IN_DAYS)
        {
            $days = explode(',', $story->repeated_days);

            if ($endDate == $startDate)
            {
                $dates[] = $endDate;
            }
            else
            {
                $dateRanges = CarbonPeriod::create($startDate, $endDate);
                foreach ($dateRanges as $date)
                {
                    $day = Str::lower($date->dayName);
                    if (in_array($day, $days))
                    {
                        $dates[] = Carbon::parse($date);
                    }
                }
            }
        }
        return $dates;
    }

    private static function saveImages($data)
    {
        $imagePaths = '';
        if (isset($data['image']))
        {
            foreach ($data['image'] as $image)
            {

                $storyImage = $image->store(StoryPost::IMAGE_PATH, 'public');
                $oldFileName = str_replace('.' . $image->extension(), '', $image->hashName());
                $newFileName = StoryPost::IMAGE_PATH . '/' . $oldFileName . '.jpg';
                Image::make(Storage::disk('public')->get($storyImage))->resize('1080', '1350')->encode('jpg')->save('storage/' . $newFileName);
                $imagePaths .= $newFileName . ',';
            }
        }
        if (isset($data['images_from_product']))
        {
            foreach ($data['images_from_product'] as $imageUrl)
            {
                $oldFilePath = Str::replace(request()->getSchemeAndHttpHost() . '/storage/', '', $imageUrl);
                if (Storage::disk('public')->exists($oldFilePath))
                {
                    $oldFileName = explode('.', $oldFilePath)[0];
                    $newFileName = $oldFileName . '.jpg';
                    $newFileName = StoryPost::IMAGE_PATH . '/' . str_replace('images/products/', '', $newFileName);
                    $newFileName = str_replace('//', '/', $newFileName);
                    Image::make(Storage::disk('public')->get($oldFilePath))->encode('jpg')->resize('1080', '1350')->save('storage/' . $newFileName);
                    $imagePaths .= $newFileName . ',';
                }
            }
        }
        return $imagePaths;
    }
}
