<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInfluencerPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('influencer_platforms', function (Blueprint $table) {
            $table->id();
            $table->string('social_id')->comment("On the Platform, like facebook id");
            $table->string('name');
            $table->integer('followers')->default(0);
            $table->integer('engagements')->default(0);
            $table->integer('avg_engagements_per_post')->default(0);
            $table->string('token')->nullable();
            $table->json('additional_info')->nullable();
            $table->foreignId('influencer_id')->references('id')->on('users');
            $table->foreignId('platform_id')->references('id')->on('platforms');
            $table->unique(['influencer_id', 'platform_id']);
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
        Schema::dropIfExists('influencer_platforms');
    }
}
