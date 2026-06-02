<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Modules\Influencer\Entities\InfluencerShopProducts;
use Modules\Influencer\Entities\Product;
use Modules\Influencer\Entities\Shops;
use Modules\Platform\Services\GenerateVideoService;
use Illuminate\Support\Str;
use Modules\Platform\Entities\StoryPost;
use Modules\AliExpress\Jobs\AliExpressUpdateProductInfoJob;
use Modules\User\Entities\User;
use Modules\User\Jobs\RemovePrivilegesRegistration;

if (!function_exists('conversionIntoKformat')) {
    function conversionIntoKformat($input)
    {
        if ($input >= 1000000) {
            return number_format(($input / 1000), 2) . "M";
        } else if ($input >= 1000) {
            return number_format(($input / 1000), 2) . "K";
        } else {
            return $input;
        }
    }
}
function image_exist($image_path): string
{
    if ($image_path) {
        return config('app.dev_storage_url') . $image_path;
    }

    return asset('landing/images/profile-staff.png');
}

function descriptionFormatter($description): string
{
    if ($description) {
        $length = 100;
        if (strlen($description) > $length) {
            return "<span class='mt-2' title='$description' >" . substr($description, 0, $length) . ' ...' . "</span>";
        }
        return "<span class='mt-2' >" . $description . "</span>";
    }
    return "<span class='mt-2'>N/A</span>";
}

function thousandsFormat($num)
{

    if ($num > 1000) {
        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x_array[0] . ((int)$x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];
        return $x_display;
    }

    return $num;
}

function splitDescription($description)
{
    $result = [];
    if (strlen($description) >= 99) {
        $result[] = substr($description, 0, 99);
        $result[] = substr($description, 99) . " &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
        return $result;
    }
    $result[] = $description;
    $result[] = "";
    return $result;
}

function getFirstLetterFromName($name)
{
    $ret = '';
    foreach (explode(' ', $name) as $word) {
        $ret .= strtoupper($word[0]);
    }
    return strlen($ret) == 1 ? $ret[0] : $ret[0] . $ret[Str::length($ret) - 1];
}


function generateVideo($files, $buy_link, $with_subtile = false, $return_absolute = false, $product = null)
{
    $generate_video = new GenerateVideoService($files);
    $generate_video->generateVideoCommand();
    // $command = $generate_video->getCommand();
    $output_1 = $generate_video->runCommand();

    if ($with_subtile) {
        $generate_video->addSubtitle($buy_link);
        $generate_video->generateVideoWithSubtitleCommand();
        // $command_with_subtile = $generate_video->getCommandWithSubtile();
        $output_2 = $generate_video->runCommand(true);
    }
    if ($product) {
        $generate_video->saveProductVideo($product, $with_subtile);
    }

    return $generate_video->getOutput($with_subtile, $return_absolute);
}


function getAccountDomainFromProductId($product_id, $user_type)
{
    if ($user_type == User::InfluencerType) {
        $product_query = InfluencerShopProducts::select('influencer_id as user_id')
            ->where('id', $product_id);
    } else {
        $product_query = Product::select('user_id')
            ->where('id', $product_id);
    }

    $user_id = $product_query->first()?->user_id;
    $shop_domain = Shops::select('id', 'user_id', 'domain')
        ->where('user_id', $user_id)
        ->first()
        ?->domain;
    return $shop_domain;
}


function addDotToSubDomain($account)
{
    if ($account && !Str::endsWith($account, ['.'])) {
        return "$account.";
    }
}

function removeDotFromSubDomain($account)
{
    if (Str::endsWith($account, ['.'])) {
        return Str::beforeLast($account, '.');
    }
    return $account;
}

function removeCouponSessionCache()
{
    cache()->forget(session('coupon_key'));
    session()->remove('coupon_key');
    session()->remove('coupon_code');
    session()->remove('coupon_buyer');
}

function registerPrivilegesToUser($user)
{
    $user->update(['privilege' => config('twiva.privilege.new-registration-user')]);
    RemovePrivilegesRegistration::dispatch($user)->delay(now()->addDays(2));
}

function checkSubdomainAccount($filter)
{
    $account = removeDotFromSubDomain(request('account'));
    if ($account) {
        $shop_user = Shops::select('id', 'user_id')->with('user:id,type')->where('domain', $account)->first();
        $filter['user_type'] = $shop_user?->user?->type;
        $filter['user_id'] = $shop_user?->user?->id;
    }
    return $filter;
}


function referralPrivilegesToUser($user)
{
    $user->increment('privilege', config('twiva.privilege.referral-user'));
}


function checkIfCouponFullyCoversOrder()
{
    return !is_null(session('coupon_with_shipping_fees')) && session('coupon_with_shipping_fees') == 0;
}


function updateRunScriptFieldInStoryPost($story)
{
    $story->update(['script_run_count' => ++$story->script_run_count]);
    $story = $story->fresh();
    if (($story->end_after_type == StoryPost::END_AFTER_TYPE_AFTER_DATE) && (!Carbon::parse($story->end_date)->diffInDays(now()))) {
        $story->update(['is_completed' => '1']);
    }
    if (($story->end_after_type == StoryPost::END_AFTER_TYPE_AFTER_COUNT_OF_TIMES) && ($story->script_run_count == $story->number_of_time)) {
        $story->update(['is_completed' => '1']);
    }
}




function uploadFile($file, $put_in = null, $disk = 'public')
{
    $path = Storage::disk($disk)->put($put_in, $file);
    return $path;
}


function uploadBinaryFile($file, $put_in = null, $disk = 'public')
{
    $put_in .= '/' . Str::uuid() . '.png';
    $path = Storage::disk($disk)->put($put_in, $file);
    if ($path) {
        return $put_in;
    }
    return null;
}


function extractFirstUrl($text)
{
    $pattern = '/https?:\/\/[^\s]+/';
    preg_match($pattern, $text, $matches);
    return $matches[0] ?? null;
}

function generateThumbnail($url)
{
    if (Str::contains($url, 'youtube')) {
        $youtube_id = Str::after($url, '?v=');
        $response = Http::get("https://img.youtube.com/vi/$youtube_id/0.jpg");
    } else {
        try{
            $response = Http::get("https://image.thum.io/get/width/600/$url");
        }catch(Exception $exception){
            $response = null;
            logger('Error when get thumbnail from image.thum.io service', ['exception-message' => $exception->getMessage(),'exception' => $exception]);
        }
    }
    return $response;
}


function getAliExpressAccount()
{
    return cache()->remember('ali-express-account', now()->addMinutes(60), function () {
        return User::where('email', config('twiva.ali-express.business-account'))->first();
    });
}

function updateInfoProductAliExpress($product_id)
{
    if ($product_id) {
        AliExpressUpdateProductInfoJob::dispatch($product_id);
    }
}
