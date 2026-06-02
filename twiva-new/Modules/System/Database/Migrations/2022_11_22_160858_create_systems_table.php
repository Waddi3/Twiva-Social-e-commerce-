<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\System\Entities\System;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system', function (Blueprint $table) {
            $table->id();
            $table->boolean('shouldSendSMSNotificationsToBuyers')->default(true);
            $table->boolean('shouldSendSMSNotificationsToInfluencersAndBusinesses')->default(true);
        });
        System::create(["shouldSendSMSNotificationsToBuyers" => true, "shouldSendSMSNotificationsToInfluencersAndBusinesses" => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('systems');
    }
};
