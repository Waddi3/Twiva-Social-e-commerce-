<?php

namespace Modules\Home\Http\Controllers\API\V2;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\User\Entities\User;

define("SUCCESS",200);
define("NO_CONTENT",204);
define("BAD_REQUEST",400);
define("UNAUTHORISED",401);
define("NOT_FOUND",404);

trait BaseTrait
{
    public function uploadImage($request_profile,String $dir="/app/public/uploads/images/business",$multiple = false){
        /* user can send single or multiple image in first argument can accept request and image path also*/

        /* all images are saving in storage folder which is root*/
        $return_data = "";
        $profile = null;
        if($multiple){
            $profile = $request_profile;
        }else if($request_profile->hasFile("profile")){
            $profile = $request_profile->file("profile");
        }

        if($profile){
            $path = storage_path().$dir;
            $file_name = $profile->getClientOriginalName();
            $file_name = uniqid()."_".$file_name;
            $file_name = str_replace(array(" ","(",")"),"",$file_name);
            if(!file_exists($path)){
                mkdir(url($dir));
            }

            $profile->move($path,$file_name);
           //chmod($path,0775);
            $return_data = $file_name;
        }

         return $return_data;
    }

    public function updateUser(int $user_id,array $data):bool{
        return User::find($user_id)->fill($data)->save();
    }

     public function uploadProfile($user_id,$image,$image_folder_name)
     {
        $image_name = Str::random(20)."_$user_id.".$image->getClientOriginalExtension();
        $path = Storage::putFileAs("public/$image_folder_name", $image, $image_name);
        $img_url = "public/storage/$image_folder_name/".$image_name;
        return $img_url;
     }

    function send_android_notification($device_token,$message,$notmessage='',$msgsender_id=''){
    if (!defined('API_ACCESS_KEY'))
    {
      define('API_ACCESS_KEY','AAAAw_qk_lI:APA91bHb0bLFByFcj3gd8umTYG5QzOFLUVWkzXudN9xHAXl232plDra86kXHohP-vAq27SM1mYT4q-FuI4OQj135k1D6_XVjF6w3uHZaFJxvR4tQO-Zi725juBHnBMB_jDWOm4eC5VMy');
    }
    $registrationIds = array($device_token);
    $fields = array(
      'registration_ids' => $registrationIds,
      'alert' => $message,
      'sound' => 'default',
      'Notifykey' => $notmessage,
      'data'=>$msgsender_id

    );
    $headers = array(
      'Authorization: key=' . API_ACCESS_KEY,
      'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields) );
    $result = curl_exec($ch);

    if($result == FALSE) {
      die('Curl failed: ' . curl_error($ch));
    }

    curl_close( $ch );
    return  $result;
  }

public static function send_iphone_notification($recivertok_id,$message,$notmessage='',$notfy_message){
    //$PATH = dirname(dirname(dirname(dirname(__FILE__))))."/pemfile/lens_development_push.pem";
    $PATH = dirname(dirname(dirname(dirname(__FILE__))))."/pemfile/apns-prod.pem";
    $deviceToken = $recivertok_id;
    $passphrase = "";
    $message = $message;
    $ctx = stream_context_create();
         stream_context_set_option($ctx, 'ssl', 'local_cert', $PATH);
         stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

      $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err,
    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

      /*$fp = stream_socket_client(
                 'ssl://gateway.push.apple.com:2195', $err,
     $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
      */
    $body['message'] = $message;
    $body['post_id'] = $notfy_message;
    $body['Notifykey'] = $notmessage;
            if (!$fp)
                 exit("Failed to connect: $err $errstr" . PHP_EOL);

        $body['aps'] = array(
            'alert' => $message,
            'sound' => 'default',
            'details'=>$body
        );

    $payload = json_encode($body);
    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
    $result = fwrite($fp, $msg, strlen($msg));

    //echo "<pre>";
    // print_r($result);
    // if (!$result)
      // echo 'Message not delivered' . PHP_EOL;
    // else
      // echo 'Message successfully delivered' . PHP_EOL;
    // exit;
    fclose($fp);
    return $result;
    die;
  }



}
