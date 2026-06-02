<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('referal_code', 'referral_code');
            $table->renameColumn('profile_completed', 'is_profile_completed');
            DB::table('users')->update(['mobile_expiry_time' => null]);
            $table->string('type')->change();
            $table->string('profile_image')->change();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('phone_number')->change();


        });
        Schema::table('influencer_detail_new', function (Blueprint $table) {
            $table->string('gender')->change();
        });

    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('is_profile_completed', 'profile_completed');
        });

    }
};