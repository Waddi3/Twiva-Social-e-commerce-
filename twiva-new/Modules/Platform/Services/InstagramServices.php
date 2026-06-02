<?php


namespace Modules\Platform\Services;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Platform\Entities\Platform;
use Modules\Platform\Entities\StoryPost;
use Modules\Platform\Entities\UserPlatform;
use Modules\Platform\Jobs\PublishToInstagramJob;
use Modules\User\Entities\User;

class  InstagramServices
{


    public const version = 'v20.0';
    private static function getInstagramActivitiesFromScrapper($influencer)
    {
        try {
            $instagramAccount = $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->first();
            $client = new Client();
            $request = $client->request(
                'POST',
                'https://business.notjustanalytics.com/analyze/getProfileInfo',
                [
                    'form_params' => [
                        'username' => $instagramAccount->social_id,
                        'type' => 'free',
                        'profile' => 'none'
                    ]
                ]
            );
            $contents = json_decode($request->getBody()->getContents());
            $engagments = ($contents->like_on_comment ?? 0) + ($contents->check_like_var ?? 0) + ($contents->avg_comment ?? 0);

            $media_count = $contents->media_count ?? 1;
            $media_count = $media_count == 0 ? 1 : $media_count;
            $instagramAccount->update(
                [
                    'followers' => $contents->follower ?? 0,
                    'engagements' => $engagments,
                    'avg_engagements_per_post' => $engagments / $media_count,
                ]
            );
        } catch (Exception $exception) {
            Log::error('Social Medial Services Instagram error 138 | Modules/Platform/Services/InstagramServices.php:47 => ', ['detail' => $exception, 'User' => $influencer]);
        }
    }


    private static function getInstagramBasicInfo($influencer, $instagram_business_account)
    {
        $client = new Client();
        $request = $client->request('GET', 'https://graph.facebook.com/' . $instagram_business_account . '/', [
            'query' => [
                'access_token' => $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->value('token'),
                'fields' => 'username,name'
            ]
        ]);
        $contents = json_decode($request->getBody()->getContents());
        UserPlatform::updateOrCreate(
            ['user_id' => $influencer->id, 'platform_id' => Platform::InstagramID],
            [
                'social_id' => $contents->username,
                'name' => $contents->name,
                //                'followers' => $contents->followers_count,
            ]
        );
    }

    public static function postToInstagram(User $influencer, $storyID): bool
    {
        $story = StoryPost::find($storyID);
        $instagramData = $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->first();
        $instagramBusinessID = json_decode($instagramData->additional_info)?->business_id;
        if (!$instagramBusinessID) {
            $instagramData->update(['token' => null]);
            return false;
        }
        $accessToken = $instagramData->token;

        if (!$accessToken) {
            return false;
        }

        if (count($story->images) > 1) {
            self::InstagramMultiImagePost($story, $instagramBusinessID, $accessToken, $influencer);
        } else {
            self::InstagramSingleImagePost($story, $instagramBusinessID, $accessToken, $influencer);
        }

        return true;
    }

    private static function InstagramMultiImagePost($story, $instagramBusinessID, $accessToken, $influencer): void
    {
        $imageContainers = [];
        $client = new Client();
        foreach ($story->images as $image) {
            try {
                $request = $client->request(
                    'POST',
                    "https://graph.facebook.com/" . self::version . "/$instagramBusinessID/media",
                    [
                        'form_params' => [
                            'access_token' => $accessToken,
                            'image_url' => $image,
                            'is_carousel_item' => 'true',
                        ]
                    ]
                );
                $imageContainers[] = json_decode($request->getBody())->id;
            } catch (Exception | GuzzleException $exception) {
                Log::error('1- error when posting to instagram | Modules/Platform/Services/InstagramServices.php:110 => ', ['error' => $exception, 'images' => $story->images]);
            }
        }
        if ($imageContainers) {
            $creationID = '';
            try {
                $request = $client->request(
                    'POST',
                    "https://graph.facebook.com/$instagramBusinessID/media",
                    [
                        'form_params' => [
                            'access_token' => $accessToken,
                            'media_type' => 'CAROUSEL',
                            'caption' => self::prepareCaption($story, $influencer), // $story->caption,
                            'children' => $imageContainers
                        ]
                    ]
                );
                $creationID = json_decode($request->getBody())->id;
            } catch (Exception | GuzzleException $exception) {
                Log::error('2- error when posting to instagram | Modules/Platform/Services/InstagramServices.php:127 => ', ['error' => $exception, '$imageContainers' => $imageContainers]);
            }

            try {
                $client->request(
                    'POST',
                    "https://graph.facebook.com/$instagramBusinessID/media_publish",
                    [
                        'form_params' => [
                            'access_token' => $accessToken,
                            'creation_id' => $creationID,
                        ]
                    ]
                );
                updateRunScriptFieldInStoryPost($story);
            } catch (Exception | GuzzleException $exception) {
                Log::error('3- error when posting to instagram | Modules/Platform/Services/InstagramServices.php:139 => ', ['error' => $exception, '$creationID' => $creationID]);
            }
        }
    }

    private static function InstagramSingleImagePost($story, $instagramBusinessID, $accessToken, $influencer): void
    {
        $client = new Client();
        $imageContainers = '';
        try {
            $image = $story->images[0] ?? $story->images;
            $request = $client->request(
                'POST',
                "https://graph.facebook.com/" . self::version . "/$instagramBusinessID/media",
                [
                    'form_params' => [
                        'access_token' => $accessToken,
                        'image_url' => $image,
                        'caption' => self::prepareCaption($story, $influencer), // $story->caption,
                    ]
                ]
            );
            $imageContainers = json_decode($request->getBody())->id;
        } catch (Exception | GuzzleException $exception) {
            Log::error('InstagramSingleImagePost 1- error when posting to instagram | Modules/Platform/Services/InstagramServices.php:161 => ', ['error' => $exception, 'images' => $story->images]);
        }

        if ($imageContainers) {
            try {
                $client->request(
                    'POST',
                    "https://graph.facebook.com/$instagramBusinessID/media_publish",
                    [
                        'form_params' => [
                            'access_token' => $accessToken,
                            'creation_id' => $imageContainers,
                        ]
                    ]
                );
            } catch (Exception | GuzzleException $exception) {
                Log::error('2- error when posting to instagram | Modules/Platform/Services/InstagramServices.php:174 => ', ['error' => $exception, '$imageContainers' => $imageContainers]);
            }
        }
    }

    public static function getInstagramBusinessID($influencer)
    {
        $client = new Client();
        $request = $client->request('GET', 'https://graph.facebook.com/' . self::version . '/me/accounts', [
            'query' => [
                'access_token' => $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->value('token'),
            ]
        ]);
        $contents = json_decode($request->getBody()->getContents());
        if (isset($contents->data[0])) {
            $page_access_token = $contents->data[0]->access_token;
            $page_id = $contents->data[0]->id;
            $request = $client->request('GET', 'https://graph.facebook.com/' . $page_id . '/', [
                'query' => [
                    'access_token' => $page_access_token,
                    'fields' => 'instagram_business_account'
                ]
            ]);
            $result = json_decode($request->getBody()->getContents());
            $instagramID = $result?->instagram_business_account?->id ?? false;
            if ($instagramID) {
                $page_access_token = MetaServices::getLongLifeToken($page_access_token);
                self::getInstagramBasicInfo($influencer, $instagramID);
                return [$instagramID, $page_access_token];
            }
        }
        return false;
    }

    public static function prepareCaption($story, $influencer)
    {
        $caption = $story->caption;
        //        $buyLink = route('shop.product.show', ['account' => $influencer->shop->domain, 'product' => $story->product_id]);
        $buyLink = self::prepareBuyLink($story, $influencer);
        $caption .= " $buyLink";
        return $caption;
    }

    public static function reelToInstagram(User $influencer, $storyID): bool
    {
        $story = StoryPost::find($storyID);
        $instagramData = $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->first();
        $instagramBusinessID = json_decode($instagramData->additional_info)?->business_id;
        if (!$instagramBusinessID) {
            $instagramData->update(['token' => null]);
            return false;
        }
        $accessToken = $instagramData->token;

        if (!$accessToken) {
            return false;
        }

        $imageContainers = self::uploadVideoIgContainerId($story, $instagramBusinessID, $accessToken, $influencer);
        if ($imageContainers) {
            sleep(10);
            $is_published = InstagramServices::checkTheContainerStatus($imageContainers, $accessToken);
            return InstagramServices::publishToInstagram($story, $instagramBusinessID, $accessToken, $influencer, $imageContainers);
            // PublishToInstagramJob::dispatch($story, $instagramBusinessID, $accessToken, $influencer, $imageContainers);
            // return true;
        }
        return false;
    }

    private static function uploadVideoIgContainerId($story, $instagramBusinessID, mixed $accessToken, User $influencer, $media_type = 'REELS')
    {
        $client = new Client();
        /**
         * POST https://graph.facebook.com/{api-version}/{ig-user-id}/media
         * ?media_type=REELS
         * &video_url={reel-url}
         * &caption={caption}
         * &share_to_feed={share-to-feed}
         * &collaborators={collaborator-usernames}
         * &cover_url={cover-url}
         * &audio_name={audio-name}
         * &user_tags={user-tags}
         * &location_id={location-id}
         * &thumb_offset={thumb-offset}
         * &access_token={access-token}
         */

        $files = $story->absolute_images->implode(' ');
        $buy_link = self::prepareBuyLink($story, $influencer);
        $create_video_with_subtitle = false;
        if ($media_type == 'STORIES') {
            $create_video_with_subtitle = true;
        }
        $video_url = generateVideo($files, $buy_link, $create_video_with_subtitle);
        //        logger('public video url => ', ['video_url' => $reel_url]);
        if (!$video_url) {
            return [];
        }
        try {
            $form_params = [
                'media_type' => $media_type,
                'video_url' => $video_url,
                'access_token' => $accessToken,
                'caption' => self::prepareCaption($story, $influencer), // $story->caption,
            ];
            //            if ($media_type == 'REELS') {
            //                $form_params['share_to_feed'] = 'true';
            //            }
            $request = $client->request(
                'POST',
                "https://graph.facebook.com/" . self::version . "/$instagramBusinessID/media",
                [
                    'form_params' => $form_params
                ]
            );
            $imageContainers = json_decode($request->getBody())->id;
            //            self::removeVideoFromStorage($video_url);
            //            dd($imageContainers);
            sleep(5);
        } catch (GuzzleException $exception) {
            $info = [
                'exception' => $exception->getMessage(),
                'media_type' => $media_type,
                'video_url' => $video_url,
                'instagramBusinessID' => $instagramBusinessID,
                'accessToken' => $accessToken,
                'story' => $story,
                'influencer' => $influencer,
            ];
            logs()->error("Modules/Platform/Services/InstagramServices.php:292 => uploadVideoIgContainerId => ", $info);
        }
        if (isset($imageContainers) and $imageContainers) {
            return [$imageContainers];
        }
        return [];
    }

    // public static function generateReelVideo(string $files, string $buy_link, $create_video_with_subtitle = false, $return_absolute = false)
    // {
    //     if (!$files) {
    //         return "";
    //     }
    //     $exists_files = [];
    //     $arr_files = explode(' ', $files);
    //     foreach ($arr_files as $file) {
    //         $exists = Storage::disk('public')->exists($file);
    //         if ($exists and Str::contains($file, ['.jpg', '.jpeg', '.png', '.webp'])) {
    //             $exists_files[] = storage_path('app/public/' . $file);
    //         }
    //     }
    //     $exists_files = Arr::join($exists_files, ' ');
    //     $uuid_name = Str::uuid();
    //     $output_name = $uuid_name . '.mp4';
    //     $output_name_with_subtitle = $uuid_name . '_with_subtitle.mp4';
    //     $video_path = 'videos';
    //     $output_path = "$video_path/$output_name";
    //     $output_name_with_subtitle_path = "$video_path/$output_name_with_subtitle";
    //     $output_absolute_path = storage_path("app/public/$video_path/$output_name");
    //     $output_absolute_path_with_subtitle = storage_path("app/public/$video_path/$output_name_with_subtitle");
    //     $logo_path = public_path('front/images/logos/Logo.png');
    //     // https://shotstack.io/learn/use-ffmpeg-to-convert-images-to-video/
    //     // cat IMG_20220106_200010.jpg IMG_20220106_200043.jpg | ffmpeg -y -framerate 0.5 -f image2pipe -i - -c:v libx264 -r 30 -t 30 -pix_fmt yuv420p output.mp4
    //     $ffmpeg = env('FFMPEG_BINARIES', 'ffmpeg');
    //     $command = "cat $exists_files | $ffmpeg -y -framerate 0.5 -f image2pipe -i - -c:v libx264 -r 30 -t 30 -pix_fmt yuv420p $output_absolute_path";
    //     //        $command = "cat /home/hossien/test.bash | grep cd";
    //     //        dd($command);
    //     $output = shell_exec($command);

    //     if ($create_video_with_subtitle) {
    //         $escapedUrl = Str::replace(['https://', 'http://'], '', $buy_link);
    //         //            $text_on_story = "For more information and purchase link please check the latest reels.";
    //         //        add test to video
    //         //        ffmpeg -y -i inputClip.mp4 -vf "drawtext=text='Centered Text':x=(w-text_w)/2:y=(h-text_h)/2:fontsize=100:fontcolor=white" -c:a copy output.mp4
    //         //            ffmpeg -y -i input.mp4 -vf "drawtext=text='test text showing':x=(w-text_w)/2:y=(h-text_h)/6:fontsize=36:fontcolor=#8d0e49" -c:a copy output.mp4
    //         //            ffmpeg -y -i input.mp4 -i logo.png -filter_complex "[0:v]drawtext=text='test text showing':x=(w-text_w)/2:y=(h-text_h)/6:fontsize=48:fontcolor=white:box=1:boxborderw=10:boxcolor=#de2259[outv];[outv][1:v]overlay=10:10[outv]" -map "[outv]" -c:a copy output.mp4
    //         //            $ffmpeg -y -i $output_absolute_path -i $logo_path -filter_complex \"[0:v]drawtext=text='https://influencer-test.dev.twiva.co.ke/43920/show':x=(w-text_w)/2:y=(h-text_h)/6:fontsize=48:fontcolor=white:box=1:boxborderw=10:boxcolor=#de2259[outv];[outv][1:v]overlay=10:10[outv]\" -map \"[outv]\" -c:a copy $output_absolute_path_with_subtitle
    //         $command_add_text_to_video = "$ffmpeg -y -i $output_absolute_path -i $logo_path -filter_complex \"[0:v]drawtext=text='$escapedUrl':x=(w-text_w)/2:y=(h-text_h)-100:fontsize=36:fontcolor=white:box=1:boxborderw=100:boxcolor=gray@0.7[outv];[outv][1:v]overlay=10:10[outv]\" -map \"[outv]\" -c:a copy $output_absolute_path_with_subtitle";
    //         //        dd($command_add_text_to_video);
    //         $output_add_text_to_video = shell_exec($command_add_text_to_video);
    //         //            Storage::disk('public')->delete($output_path);
    //         if ($return_absolute) {
    //             return $output_name_with_subtitle_path;
    //         }
    //         return asset('storage/' . $output_name_with_subtitle_path);
    //     }
    //     //        dd($output);
    //     // check $output
    //     if ($return_absolute) {
    //         return $output_path;
    //     }
    //     return asset('storage/' . $output_path);
    // }

    public static function publishToInstagram($story, $instagramBusinessID, mixed $accessToken, User $influencer, $imageContainers)
    {
        //         /{{api_version}}/{{ig_user_id}}/media_publish?creation_id={{ig_container_id}}&access_token={{page_access_token}}
        $result = [];
        foreach ($imageContainers as $image_Container) {
            $client = new Client();
            try {
                $client->request(
                    'POST',
                    "https://graph.facebook.com/" . self::version . "/$instagramBusinessID/media_publish",
                    [
                        'form_params' => [
                            'access_token' => $accessToken,
                            'creation_id' => $image_Container,
                        ]
                    ]
                );
                $result[] = true;
            } catch (Exception | GuzzleException $exception) {
                logs()->error('Modules/Platform/Services/InstagramServices.php:311 => publishToInstagram => ', ['error' => $exception->getMessage(), 'creationID' => $image_Container]);
                $result[] = false;
            }
        }
        $filtered = Arr::where($result, function ($value) {
            return $value;
        });
        return count($filtered) == count($result);
    }

    public static function storyToInstagram(User $influencer, $storyID): bool
    {
        $story = StoryPost::find($storyID);
        $instagramData = $influencer->UserPlatforms()->where('platform_id', Platform::InstagramID)->first();
        $instagramBusinessID = json_decode($instagramData->additional_info)?->business_id;
        if (!$instagramBusinessID) {
            $instagramData->update(['token' => null]);
            return false;
        }
        $accessToken = $instagramData->token;

        if (!$accessToken) {
            return false;
        }

        //        $imageContainers = self::storyIgContainerId($story, $instagramBusinessID, $accessToken, $influencer);
        $imageContainers = self::uploadVideoIgContainerId($story, $instagramBusinessID, $accessToken, $influencer, 'STORIES');
        if (count($imageContainers)) {
            sleep(15);
            $is_published = InstagramServices::checkTheContainerStatus($imageContainers, $accessToken);
            return InstagramServices::publishToInstagram($story, $instagramBusinessID, $accessToken, $influencer, $imageContainers);
            // PublishToInstagramJob::dispatch($story, $instagramBusinessID, $accessToken, $influencer, $imageContainers);
            // return true;
        }
        return false;
    }

    private static function storyIgContainerId($story, $instagramBusinessID, mixed $accessToken, User $influencer)
    {
        $client = new Client();
        /**
         * POST https://graph.facebook.com/{api-version}/{ig-user-id}/media
         * ?image_url={image-url}
         * &media_type=STORIES
         * &access_token={access-token}
         */

        $storiesContainers = [];
        foreach ($story->images as $image) {
            try {
                $request = $client->request(
                    'POST',
                    "https://graph.facebook.com/" . self::version . "/$instagramBusinessID/media",
                    [
                        'form_params' => [
                            'media_type' => 'STORIES',
                            'image_url' => $image,
                            'access_token' => $accessToken,
                        ]
                    ]
                );
                $storiesContainers[] = json_decode($request->getBody())->id;
            } catch (GuzzleException $exception) {
                $info = [
                    'exception' => $exception->getMessage(),
                    'story' => $story,
                    'instagramBusinessID' => $instagramBusinessID,
                    'accessToken' => $accessToken,
                    'influencer' => $influencer,
                ];
                logs()->error("Modules/Platform/Services/InstagramServices.php:397 => storyIgContainerId => ", $info);
            }
        }
        if (count($storiesContainers)) {
            return $storiesContainers;
        }
        return [];
    }

    public static function checkTheContainerStatus(array $imageContainers, $accessToken, $retry = 5)
    {
        /**
         * GET https://graph.facebook.com/{instagram-container-id}
         * ?fields={fields}
         * &access_token={access-token}
         */
        for ($i = 0; $i < $retry; $i++) {
            sleep(5);
            $results = [];
            foreach ($imageContainers as $imageContainer) {
                $client = new Client();
                try {
                    $request = $client->request(
                        'GET',
                        "https://graph.facebook.com/$imageContainer",
                        [
                            'form_params' => [
                                'fields' => 'id,status,status_code',
                                'access_token' => $accessToken,
                            ]
                        ]
                    );
                    $response = json_decode($request->getBody());
                    $results[] = $response->status_code == 'FINISHED';
                } catch (GuzzleException $exception) {
                    $results[] = false;
                }
            }
            $filtered = Arr::where($results, function ($value) {
                return $value;
            });
            if (count($filtered) == count($results)) {
                return true;
            }
        }
        return false;
    }

    private static function removeVideoFromStorage(string $video_url)
    {
        $file_to_remove = Str::after($video_url, '/storage/');
        Storage::disk('public')->delete($file_to_remove);
    }

    public static function prepareBuyLink($story, User $influencer)
    {
        return route('shop.product.show', ['account' => $influencer->shop->domain, 'product' => $story->product_id]);
    }
}
