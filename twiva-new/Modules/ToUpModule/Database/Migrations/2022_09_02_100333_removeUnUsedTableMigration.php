<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bloggers', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('blogs', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('gigs', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('gigslist', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('gig_details', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('gig_media', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('gig_media_details', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('gig_prices', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('gig_users', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__bookmark', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__central_columns', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__column_info', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__designer_settings', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__export_templates', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__favorite', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__history', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__navigationhiding', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__pdf_pages', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__recent', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__relation', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__savedsearches', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__table_coords', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__table_info', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__table_uiprefs', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__tracking', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__userconfig', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__usergroups', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('pma__users', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('reasons', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('services', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('share_data', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('sidebars', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('timelines', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('timeline_images', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('timeline_likes', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('verifydata', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('subscription', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('notifications_new', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('influencer_details', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('bidding_post', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('bidding_justification', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('all_images', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('category_users', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('contact_support', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('featureds', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('influencer_images', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('influencer_price', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('influencer_wait_lists', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('intersts_users', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('invitation_user', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('porfolios', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('social_engagements', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('subadmin_permissions', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('user_details', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('refferal_codes', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('user_subscriptions', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('user_social_connection', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('temp_tokens', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('ratings', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('ratings', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('rates', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('post_details_media', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('infulancer_ratings', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('influencer_social_details', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('admins', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });
        Schema::table('business_details', function (Blueprint $blueprint) {
            $blueprint->dropIfExists();
        });

        Schema::table('interests', function (Blueprint $table) {
            $table->dropIfExists();
        });
        Schema::table('influencers_social_categories', function (Blueprint $table) {
            $table->dropIfExists();
        });
        Schema::table('social_categories', function (Blueprint $table) {
            $table->dropIfExists();
        });
        Schema::table('interests', function (Blueprint $table) {
            $table->dropIfExists();
        });
        Schema::table('influencer_interests', function (Blueprint $table) {
            $table->dropIfExists();
        });
        Schema::table('mpesa_phone_numbers', function (Blueprint $table) {
            $table->dropIfExists();
        });
        Schema::table('user_device_token', function (Blueprint $table) {
            $table->dropIfExists();
        });
        Schema::table('shopping_cart', function (Blueprint $table) {
            $table->dropIfExists();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('interests', function (Blueprint $table) {
            $table->string('id');
            $table->string('user_id');
            $table->string('added_by');
            $table->string('image');
            $table->string('interest_name');
            $table->string('created_at');
            $table->string('updated_at');
        });
        Schema::create('influencers_social_categories', function (Blueprint $table) {
            $table->string('social_category_id');
            $table->string('influencer_id');
        });
        Schema::create('social_categories', function (Blueprint $table) {
            $table->string('id');
            $table->string('name');
            $table->string('parent_id');
        });
    }
};
