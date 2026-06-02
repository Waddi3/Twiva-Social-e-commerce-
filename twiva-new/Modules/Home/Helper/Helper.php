<?php


namespace Modules\Home\Helper;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Google\Service\ShoppingContent\AccountUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;
use Modules\Campaign\Entities\UserCompany;


class Helper
{
    public static function stringToDateObject($dateString)
    {
        return Carbon::createFromTimeString($dateString);
    }

    public static function route($name, $options = [])
    {
        return route($name, $options);
    }

    public static function getModuleOperation($requestUri, $permission_name = null)
    {
        $user = Auth::user();
        $uri_array = explode('/', $requestUri);

        // If we don't get any user data then it will return false
        if (empty($user)) {
            return false;
        }

        if ($user->user_module_permission === 'ALL') {
            return 'ALL';
        }
        $user_permissions = explode(',', $user->user_module_permission);

        $uri_array[1] = (array_key_exists(1, $uri_array)) ? strtoupper($uri_array[1]) : '';
        $permission = $uri_array[1];
        if ($permission_name) {
            $permission = $permission . "-" . strtoupper($permission_name);
        }
        if (in_array($permission, $user_permissions)) {
            return true;
        }

        return false;
    }


    public static function checkRouteType()
    {
        if (strpos($_SERVER['REQUEST_URI'], env('ADMIN_URL')) !== false) {
            return env('ADMIN_URL');
        } else {
            return 'user';
        }
    }

    public static function redirectToHome()
    {
        if (strpos($_SERVER['REQUEST_URI'], env('ADMIN_URL')) !== false) {
            return route('admin.dashboard');
        } else {
            return route('frontend.dashboard');
        }
    }


    // New Helper Functions
    // Last Query
    //DB->enableQueryLog();
    //$queries = DB::getQueryLog();

    public static function pr($obj)
    {
        echo '<pre>';
        print_r($obj);
        echo '</pre>';
    }

    public static function encode($arg)
    {
        return base64_encode($arg);
    }

    public static function decode($arg)
    {
        return base64_decode($arg);
    }

    public static function getFileExt($fileName = '')
    {

        if (empty($fileName))
            return false;

        $exp = explode('.', $fileName);

        if (count($exp) > 0) {
            return end($exp);
        } else {
            return false;
        }
    }

    public static function delFile($file)
    {
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    public static function appDateFormat($date = null)
    {

        if (empty($date))
            $date = date('Y-m-d');

        if (is_numeric($date)) {
            return date(config('constants.APP_DATE_FORMAT'), $date);
        } else {
            return date(config('constants.APP_DATE_FORMAT'), strtotime($date));
        }
    }

    public static function generatePatternNumber($id, $numberCount = 8)
    {
        return str_pad($id, $numberCount, "0", STR_PAD_LEFT);
    }

    public static function generateRandomStr($strLength = 8, $prefix = '')
    {

        $code = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $code .= "abcdefghijklmnopqrstuvwxyz";
        $code .= "0123456789";
        //$code .= '!@#$%^&*()';

        $token = $prefix;
        $max = strlen($code);

        for ($i = 0; $i < $strLength; $i++)
            $token .= $code[random_int(0, $max - 1)];

        return $token;
    }

    public static function generateCSV($header, $data, $filename = 'result_file.csv')
    {
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen("php://output", "w");
        fputcsv($output, $header);
        foreach ($data as $row) {
            fputcsv($output, (array) $row);
        }
        fclose($output);
    }

    public static function generateRandomStrThirtyThree($strLength = 33, $prefix = '')
    {

        $code = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $code .= "abcdefghijklmnopqrstuvwxyz";
        $code .= "0123456789";
        //$code .= '!@#$%^&*()';

        $token = $prefix;
        $max = strlen($code);

        for ($i = 0; $i < $strLength; $i++)
            $token .= $code[random_int(0, $max - 1)];

        return $token;
    }

    public static function unsetData($dataArray = array(), $unsetDataArray = array())
    {
        return array_diff_key($dataArray, array_flip($unsetDataArray));
    }

    /* FILE UPLOADS */

    public static function fileUpload($file, $destinationPath = 'uploads', $fileName = null)
    {

        $uploadTo = config('filesystems.default');  // Get from config folder configuration

        if (empty($file->getClientOriginalName()))
            return '';

        $ext = $file->getClientOriginalExtension();
        if (empty($fileName))
            $fileName = time() . '-' . mt_rand(1000, 9999) . '.' . $ext;


        if ($uploadTo == 'local') {
            // Local uploads
            //echo $destinationPath . $fileName;die;

            $file->move($destinationPath, $fileName);
            @chmod($destinationPath . $fileName, 0777);

            return $fileName;
        } else if ($uploadTo == 's3') {
            // S3 Uploads

            Storage::disk('s3')->put($destinationPath . $fileName, file_get_contents($file), 'public');
            //$fileName = Storage::disk('s3')->url($fileName);

            return $fileName;
        }
    }


    public static function generateUploadFilePath($type)
    {

        $uploadTo = config('filesystems.default');  // Get from config folder configuration

        $fileLocation = config('filelocation.' . $type); //subcontractor-profile-pic


        if ($uploadTo == 'local') {
            // Local uploads
            //echo $destinationPath . $fileName;die;

            return env('PROJECT_PATH') . env('UPLOAD_DIR') . $fileLocation;
        } else if ($uploadTo == 's3') {
            // S3
            //$awsRegion = env('AWS_REGION');
            //$bucketName = env('AWS_BUCKET');
            //return 'https://s3.'. $awsRegion .'.amazonaws.com/' . $bucketName . '/';
            return $fileLocation;
        }
    }

    public static function getUploadedFileUrl($type, $fileName)
    {

        $uploadTo = config('filesystems.default');  // Get from config folder configuration

        $fileLocation = config('filelocation.' . $type); //subcontractor-profile-pic


        if ($uploadTo == 'local') {
            // Local uploads
            //\echo $destinationPath . $fileName;die;
            return url('/') . '/' . env('UPLOAD_DIR') . $fileLocation . $fileName;
        } else if ($uploadTo == 's3') {
            // S3

            $awsRegion = env('AWS_REGION');
            $bucketName = env('AWS_BUCKET');

            return 'https://s3.' . $awsRegion . '.amazonaws.com/' . $bucketName . '/' . $fileLocation . $fileName;
        }
    }

    public static function randomOtp()
    {
        $alphabet = '1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    public static function randomCustomerId($count)
    {
        $alphabet = '1AX23BZS456YCD78OIU90HGLMNV';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $count; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    public static function callCURL($method, $url, $data, $headers = array())
    {

        // HELP URL: https://www.weichieprojects.com/blog/curl-api-calls-with-php/

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        //echo $url;//die;

        // OPTIONS:
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $headers = array(
                'APIKEY: 111111111111111111111',
                'Content-Type: application/json',
            );
        }

        // EXECUTE:
        $result = curl_exec($curl);
        if (!$result) {
            die("Connection Failure");
        }
        curl_close($curl);

        return $result;
    }

    public static function listingDateFormat($date)
    {
        return date("M d, Y", strtotime($date));
    }

    public static function convertSecondsIntoTime($time)
    {
        // $time = 111031; // time duration in seconds
        $time = abs($time);

        $days = floor($time / (60 * 60 * 24));
        $time -= $days * (60 * 60 * 24);

        $hours = floor($time / (60 * 60));
        $time -= $hours * (60 * 60);

        $minutes = floor($time / 60);
        $time -= $minutes * 60;

        $seconds = floor($time);
        $time -= $seconds;

        return array(
            "d" => str_pad($days, 2, '0', STR_PAD_LEFT),
            "h" => str_pad($hours, 2, '0', STR_PAD_LEFT),
            "m" => str_pad($minutes, 2, '0', STR_PAD_LEFT),
            "s" => str_pad($seconds, 2, '0', STR_PAD_LEFT),
        );
    }

    public static function SetCompanyIDInSession($company)
    {
        Session::put('company_id', $company);
    }

    public static function expireOtp($data)
    {

        $otpTime = Carbon::parse($data->created_at)->diffInMinutes(Carbon::now());
        $time = config('constants.otpExpireTime');
        if ($otpTime < $time) {

            return True;
        } else {

            return False;
        }
    }

    public static function expireToken($data)
    {

        $tokenTime = Carbon::parse($data->created_at)->diffInMinutes(Carbon::now());
        $time = config('constants.tokenExpireTime');
        if ($tokenTime < $time) {

            return True;
        } else {

            return False;
        }
    }

    public static function listUser($id)
    {

        $query1 = "SELECT R.role_id FROM account_user as R WHERE R.user_id = $id";

        $role1 = DB::connection('randomDB')->select(DB::raw($query1));
        $role = array();
        foreach ($role1 as $key => $value) {
            $role['role_id'] = $value->role_id;
        }
        $company = UserCompany::select('company_id')->where(['user_id' => $id])->first();
        $query = "SELECT UC.user_id,U.name,U.surname,U.deleted_at,U.status FROM pro_user_company as UC JOIN users as U ON UC.user_id = U.id";
        if (!empty($role)) {

            if ($role['role_id'] != 1) {

                //$query .= " WHERE UC.created_by =". "$id";
                $query .= " WHERE UC.user_id !=" . "$id";
                $query .= " AND UC.company_id =" . "$company->company_id";
                $query .= " AND U.deleted_at IS NULL";
                $query .= " AND U.status = 'active'";
                $query .= " AND U.type = 'company'";
            } else {

                $query .= " WHERE UC.user_id !=" . "$id";
                $query .= " AND UC.company_id =" . "$company->company_id";
                $query .= " AND U.deleted_at IS NULL";
                $query .= " AND U.status = 'active'";
                $query .= " AND U.type = 'company'";
            }
        } else {

            //$query .= " WHERE UC.created_by =". "$id";
            $query .= " WHERE U.deleted_at IS NULL";
            $query .= " AND U.status = 'active'";
            $query .= " AND U.type = 'company'";
        }

        $userList = DB::select(DB::raw($query));

        $result = array();
        if (!empty($role)) {
            $result[] = array(
                'user_id' => $id,
                'name' =>  "Myself",
            );
        }
        foreach ($userList as $key => $value) {
            $result[] = array(
                'user_id' => $value->user_id,
                'name' =>  empty($value->name) ? $value->surname : (empty($value->surname) ? $value->name : $value->name . ' ' . $value->surname),
            );
        }
        return $result;
    }

    public static function saveActivityLog($action = [])
    {

        if (env('MONGO_DB_CONFIGURED', false) == false) return true;

        $agent = new Agent();
        $location = geoip()->getLocation(request()->ip());
        $userId = Auth::user()->id;

        //get db name to make activity collection.
        $companyDetails = UserCompany::where(['user_id' => $userId])->with('Companies')->first();
        //$activityModel = new Activity;
        $activityName = $companyDetails['Companies']['domain_details'] . '_user_activity';
        $activityModel = new Activity($activityName);
        // $activityModel->setTable($activityName);
        // Helper::pr($action);die();
        $data = [
            'user_name' => Auth::user()->name . " " . Auth::user()->surname,
            'user_id' => $userId,
            'module' => $action['module'],
            'element_id' => $action['element_id'],
            'message' => $action['message'],
            //'created_on' => $action['created_on']
        ];

        $activity = [
            'referer' => request()->headers->get('referer'),
            'cookie_infos' => request()->cookies,
            'search_term' => request()->query(),
            'device_info' => [$agent->device()],
            'location' => "{$location->city}, {$location->state_name}, {$location->country}({$location->postal_code})",
            'browser_type' => $agent->browser(),
            'operating_system' => $agent->platform(),
            'ip_address' =>  request()->ip(),
            'domain' => request()->getHost(),
        ];

        $data['activity'] = $activity;

        // Helper::pr($data);die();

        //$activity = Activity::create($data);
        //$activity = $activityModel::create($data);
        $activityModel->fill($data);
        $activity = $activityModel->save();
    }

    public static function loginUserRole($userId)
    {
        $roles = AccountUser::select('role_id')->where(['user_id' => $userId])->first();
        return $roles->role_id;
    }


    public static function curlRegister($mongoUser)
    {
        $curl = curl_init();

        $data = json_encode($mongoUser);
        //Helper::pr($data);die;
        //it will receive url form env file. If key is not define in .env file then it will pic default value which is second parameter.
        $chatUrl = env("CHAT_URL", "https://chat.propositron.com/register");

        curl_setopt_array($curl, array(
            // CURLOPT_URL => "https://chat.alpinesoft.dk/register",
            //CURLOPT_URL => "https://betachat.alpinesoft.dk/register",
            CURLOPT_URL => $chatUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            //CURLOPT_POSTFIELDS => "{\n\t\"username\":\"demo\",\n\t\"password\":\"12345678\"\n}",

            /*  CURLOPT_POSTFIELDS => "{
                                    \n\t\"username\":\"".$userName."\",
                                    \n\t\"password\":\"".$password."\"\n
                                }", */
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: a9ece9f3-2861-71f8-cf70-5b550ea1fbc0"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //  echo "cURL Error #:" . $err;
            return $err;
        } else {
            //  echo $response;
            return $response;
        }
        //die('curl register');
    }
    public static function notificationDate($date)
    {

        $seconds_ago = (time() - strtotime($date));

        if (($seconds_ago > 86400)) {
            $datemsg = 'on ' . date('d/m/Y g:i a', strtotime($date));
        } elseif (($seconds_ago >= 3600) && ($seconds_ago <= 86400)) {
            $datemsg = intval($seconds_ago / 3600) . " hours ago";
        } elseif (($seconds_ago >= 60) && ($seconds_ago < 120)) {
            $datemsg = intval($seconds_ago / 60) . " minute ago";
        } elseif ($seconds_ago >= 120) {
            $datemsg = intval($seconds_ago / 60) . " minutes ago";
        } else {
            $datemsg = " a few seconds ago";
        }
        return $datemsg;
    }

    public static function getTimeToUserTimeZone($date)
    {

        $useDetail = Auth::user();
        $userTimeZone = Zone::select('zone_name')->where(['zone_id' => $useDetail['time_zone']])->first();
        //$userTimeZone = [];
        if (!empty($userTimeZone)) {
            $currentDateTime = $date;
            $dt = new DateTime($currentDateTime);
            $tz = new DateTimeZone($userTimeZone->zone_name);
            $dt->setTimezone($tz);
            $userTimeZoneDateTime = $dt->format('Y-m-d H:i:s');
            //current time
            $time = date('Y-m-d H:i:s');
            $tm = new DateTime($time);
            $tzm = new DateTimeZone($userTimeZone->zone_name);
            $tm->setTimezone($tzm);
            $tmt = $tm->format('Y-m-d H:i:s');
            $fianlData = array(
                'timeZoneTime' => $userTimeZoneDateTime,
                'currentTimeWithZone' => $tmt,
            );
            return $fianlData;
        } else {

            $currentDateTime = $date;
            $dt = new DateTime($currentDateTime);
            $tz = new DateTimeZone(config('constants.DefaultTimeZone'));
            $dt->setTimezone($tz);
            $userTimeZoneDateTime = $dt->format('Y-m-d H:i:s');
            //current time
            $time = date('Y-m-d H:i:s');
            $tm = new DateTime($time);
            $tzm = new DateTimeZone(config('constants.DefaultTimeZone'));
            $tm->setTimezone($tzm);
            $tmt = $tm->format('Y-m-d H:i:s');
            $fianlData = array(
                'timeZoneTime' => $userTimeZoneDateTime,
                'currentTimeWithZone' => $tmt,
            );
            return $fianlData;
        }
    }
    //change date format with time zone of event for raw Query
    public static function setDateFormatForList($resultData)
    {

        $i = 0;
        foreach ($resultData as $keydddd => $resultDataValue) {
            if (array_key_exists("start_date", $resultDataValue)) {
                if (!empty($resultDataValue->start_date)) {
                    $getStartDate = Helper::genralDateFormatWithZone($resultDataValue->start_date);
                    $lastStartDate = $getStartDate->format('Y-m-d H:i');
                    $finalStartDate = (!empty($lastStartDate) ? date('Y-m-d', strtotime($lastStartDate)) : '') . 'T' . (!empty($lastStartDate) ? date('H:i', strtotime($lastStartDate)) : '');
                    $resultData[$i]->start_date = $finalStartDate;
                } else {
                    $finalStartDate = $resultDataValue->start_date;
                    $resultData[$i]->start_date = $finalStartDate;
                }
            }
            if (array_key_exists("end_date", $resultDataValue)) {
                if (!empty($resultDataValue->end_date)) {
                    $getStartDate = Helper::genralDateFormatWithZone($resultDataValue->end_date);
                    $lastEndDate = $getStartDate->format('Y-m-d H:i');
                    $finalEndData = (!empty($lastEndDate) ? date('Y-m-d', strtotime($lastEndDate)) : '') . 'T' . (!empty($lastEndDate) ? date('H:i', strtotime($lastEndDate)) : '');
                    $resultData[$i]->end_date = $finalEndData;
                } else {
                    $finalEndData = $resultDataValue->end_date;
                    $resultData[$i]->end_date = $finalEndData;
                }
            }
            if (array_key_exists("due_date", $resultDataValue)) {
                if (!empty($resultDataValue->due_date)) {
                    $getStartDate = Helper::genralDateFormatWithZone($resultDataValue->due_date);
                    $lastDueData = $getStartDate->format('Y-m-d H:i');
                    $finalDueData = (!empty($lastDueData) ? date('Y-m-d', strtotime($lastDueData)) : '') . 'T' . (!empty($lastDueData) ? date('H:i', strtotime($lastDueData)) : '');
                    $resultData[$i]->due_date = $finalDueData;
                } else {
                    $finalDueData = $resultDataValue->due_date;
                    $resultData[$i]->due_date = $finalDueData;
                }
            }
            $i++;
        }
        return $resultData;
    }

    //change date format with time zone of event foe ELQ Modale
    public static function setDateFormatForListELQ($resultData)
    {
        $i = 0;
        foreach ($resultData as $keydddd => $resultDataValue) {

            if (array_key_exists("created_at", $resultDataValue->getoriginal())) {
                if (!empty($resultDataValue->created_at)) {
                    $getStartDate = Helper::genralDateFormatWithZone($resultDataValue->created_at);
                    $finalCreatedDate = $getStartDate->format('d/m/Y, H:i:s');
                    $resultData[$i]->created_at = Carbon::createFromFormat('d/m/Y, H:i:s', $finalCreatedDate);
                } else {
                    $finalCreatedDate = $resultDataValue->created_at;
                    $resultData[$i]->created_at = $finalCreatedDate;
                }
            }
            if (array_key_exists("updated_at", $resultDataValue->getoriginal())) {
                if (!empty($resultDataValue->updated_at)) {
                    $getStartDate = Helper::genralDateFormatWithZone($resultDataValue->updated_at);
                    $finalCreatedDate = $getStartDate->format('d/m/Y, H:i:s');
                    $resultData[$i]->updated_at = Carbon::createFromFormat('d/m/Y, H:i:s', $finalCreatedDate);
                } else {
                    $finalCreatedDate = $resultDataValue->updated_at;
                    $resultData[$i]->updated_at = $finalCreatedDate;
                }
            }
            $i++;
        }
        return $resultData;
    }

    //Set Time Zone For CRED
    public static function genralDateFormatWithZone($date)
    {

        $useDetail = Auth::user();
        $userTimeZone = Zone::select('zone_name')->where(['zone_id' => $useDetail['time_zone']])->first();
        //$userTimeZone = [];
        if (!empty($userTimeZone)) {
            $currentDateTime = $date;
            $dt = new DateTime($currentDateTime);
            $tz = new DateTimeZone($userTimeZone->zone_name);
            $dt->setTimezone($tz);
            //$userTimeZoneDateTime = $dt->format('d/m/Y H:i:s');
            return $dt;
        } else {
            $currentDateTime = $date;
            $dt = new DateTime($currentDateTime);
            $tz = new DateTimeZone(config('constants.DefaultTimeZone'));
            $dt->setTimezone($tz);
            return $dt;
        }
    }

    //Set UTC Time Zone
    public static function SetUtcTime($getDate) {
        $useDetail = Auth::user();
        $userTimeZone = Zone::select('zone_name')->where(['zone_id' => $useDetail['time_zone']])->first();
        if ($userTimeZone) {
            date_default_timezone_set($userTimeZone->zone_name);
            $datetime = strtotime($getDate);
            date_default_timezone_set('UTC');
            $utcDateTime = date("Y-m-d H:i:s", $datetime);
            return $utcDateTime;
        } else {

            date_default_timezone_set(config('constants.DefaultTimeZone'));
            $datetime = strtotime($getDate);
            date_default_timezone_set('UTC');
            $utcDateTime = date("Y-m-d H:i:s", $datetime);
            return $utcDateTime;
        }
    }
}
