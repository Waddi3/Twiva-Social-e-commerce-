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
        Schema::table('users', function (Blueprint $table) {
            // boolean
            $table->boolean('is_active_notification_email')->default(true);
            $table->boolean('is_active_notification_fcm')->default(true);
            $table->boolean('is_admin_for_community')->default(false);
            // timestamp
            $table->timestamp('de_active_community_at')->nullable();
            $table->timestamp('banned_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'de_active_community_at',
                'is_active_notification_email',
                'is_active_notification_fcm',
                'banned_at',
            ]);
        });
    }
};
