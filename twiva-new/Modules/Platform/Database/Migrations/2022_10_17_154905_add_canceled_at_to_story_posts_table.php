<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('user_story_post', function (Blueprint $table) {
            $table->timestamp('canceled_at')->after('caption')->nullable();
        });
    }

    public function down()
    {
        Schema::table('user_story_post', function (Blueprint $table) {
            $table->dropColumn('canceled_at');
        });
    }
};