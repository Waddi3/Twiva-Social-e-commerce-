<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('influencer_detail_new', function (Blueprint $table) {
            $table->rename('influencer_details');
        });
//        Schema::table('admin_user', function (Blueprint $table) {
//            $table->rename('admins');
//        });
        Schema::table('user_referal_codes', function (Blueprint $table) {
            $table->rename('user_referral_codes');
        });
        Schema::table('user_referral_codes', function (Blueprint $table) {
            $table->renameColumn('is_activited', 'is_activated');
        });


    }
};