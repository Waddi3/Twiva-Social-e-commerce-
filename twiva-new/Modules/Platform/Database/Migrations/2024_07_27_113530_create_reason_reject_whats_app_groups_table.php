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
        Schema::create('reason_reject_whats_app_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reason_reject_id')->constrained('reason_rejects');
            $table->foreignId('whats_app_group_id')->constrained('whats_app_groups');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reason_reject_whats_app_groups');
    }
};
