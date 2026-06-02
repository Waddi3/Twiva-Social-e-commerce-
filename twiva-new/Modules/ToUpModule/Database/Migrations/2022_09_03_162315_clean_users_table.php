<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_type');
            $table->dropColumn('avtar');
            $table->dropColumn('country_code');
            $table->dropColumn('contact_person');
            $table->dropColumn('facebook_id');
            $table->dropColumn('instagram_id');
            $table->dropColumn('twitter_id');
            $table->dropColumn('device_type');
            $table->dropColumn('device_id');
            $table->dropColumn('email_verify');
            $table->dropColumn('emailStatus');
            $table->dropColumn('is_deleted');
            $table->dropColumn('business_address');
            $table->dropColumn('country_name');
            $table->dropColumn('remember_token');
            $table->dropColumn('login_status');
            $table->dropColumn('discover_twiva');
            $table->dropColumn('other');
            $table->dropColumn('expiry_time');
            $table->dropColumn('otp');
            $table->dropColumn('descriptions');
            $table->dropColumn('user_from');
            $table->dropColumn('is_user_update_to_new_platform');
            $table->dropColumn('noinflue');
            $table->dropColumn('guestacc');
            $table->dropColumn('mobile_otp');
            $table->dropColumn('mobile_expiry_time');
            $table->dropColumn('refferal_code');
            $table->dropColumn('location');
            $table->dropColumn('dob');
            $table->dropColumn('profile');
            $table->dropColumn('is_active');
            $table->dropColumn('background_image');
            $table->dropColumn('is_phone_verified');
            $table->dropColumn('verifyacc');

        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->string('avtar')->nullable();
            $table->string('country_code')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('instagram_id')->nullable();
            $table->string('twitter_id')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_id')->nullable();
            $table->string('email_verify')->nullable();
            $table->string('emailStatus')->nullable();
            $table->string('is_deleted')->nullable();
            $table->string('business_address')->nullable();
            $table->string('country_name')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('login_status')->nullable();
            $table->string('discover_twiva')->nullable();
            $table->string('other')->nullable();
            $table->string('expiry_time')->nullable();
            $table->string('otp')->nullable();
            $table->string('descriptions')->nullable();
            $table->string('user_from')->nullable();
            $table->string('is_user_update_to_new_platform')->nullable();
            $table->string('noinflue')->nullable();
            $table->string('guestacc')->nullable();
            $table->string('expiry_time')->nullable();
            $table->string('mobile_otp')->nullable();
            $table->string('mobile_expiry_time')->nullable();
            $table->string('refferal_code')->nullable();
            $table->string('location')->nullable();
            $table->string('dob')->nullable();
            $table->string('profile')->nullable();
            $table->string('verifyacc')->nullable();
            $table->string('is_active')->nullable();
            $table->string('background_image')->nullable();
            $table->string('is_phone_verified')->nullable();
        });

    }
};