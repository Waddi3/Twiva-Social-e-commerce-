<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('user_type')->nullable()->default('users')->after('user_id');
        });
        Schema::table('post_users', function (Blueprint $table) {
            $table->string('user_type')->nullable()->default('users')->after('user_id');
        });
        Schema::table('post_prices', function (Blueprint $table) {
            $table->string('user_type')->nullable()->default('users')->after('infulancer_id')->comment('influencer_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['user_type']);
        });
        Schema::table('post_users', function (Blueprint $table) {
            $table->dropColumn(['user_type']);
        });
        Schema::table('post_prices', function (Blueprint $table) {
            $table->dropColumn(['user_type']);
        });
    }
};
