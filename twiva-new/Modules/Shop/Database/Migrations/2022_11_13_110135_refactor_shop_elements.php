<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('influencer_shop', 'shops');
        Schema::rename('influencer_platforms', 'user_platforms');
        Schema::rename('orders_details', 'order_details');
        Schema::table('user_platforms', function (Blueprint $table) {
            $table->renameColumn('influencer_id', 'user_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('store_id', 'shop_id');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign('orders_details_influencer_id_foreign');
            $table->dropColumn('influencer_id');
            $table->dropForeign("orders_details_business_id_foreign");
            $table->dropColumn("business_id");
        });
    }

    public function down()
    {

    }
};