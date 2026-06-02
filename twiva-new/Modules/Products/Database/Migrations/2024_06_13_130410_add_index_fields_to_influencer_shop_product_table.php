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
        Schema::table('influencer_shop_product', function (Blueprint $table) {
            $table->index('influencer_id');
            $table->index('product_id');
            $table->index('product_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('influencer_shop_product', function (Blueprint $table) {
            $table->dropIndex('influencer_shop_product_influencer_id_index');
            $table->dropIndex('influencer_shop_product_product_id_index');
            $table->dropIndex('influencer_shop_product_product_title_index');
        });
    }
};
