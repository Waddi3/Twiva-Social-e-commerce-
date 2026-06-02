<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('influencer_details', function (Blueprint $table) {
            $table->dropColumn('youtube_id');
            $table->dropColumn('instagram_id');
            $table->dropColumn('facebook_id');
            $table->dropColumn('twitter_id');
            $table->dropColumn('is_shop_listed');
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->dropColumn('profile_image');
        });
    }

    public function down()
    {
        Schema::table('influencer_details', function (Blueprint $table) {
            $table->string('youtube_id')->nullable();
            $table->string('instagram_id')->nullable();
            $table->string('facebook_id')->nullable();
            $table->string('twitter_id')->nullable();
            $table->string('is_shop_listed')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('gender')->change();
        });
    }
};