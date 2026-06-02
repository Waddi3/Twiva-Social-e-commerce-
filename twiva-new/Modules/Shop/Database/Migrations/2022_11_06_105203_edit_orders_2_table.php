<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('influencer_id', 'shop_id');
            $table->foreign('shop_id')->references('id')->on('shops');
        });
        DB::statement("ALTER TABLE orders MODIFY COLUMN deleted_at  DATE AFTER shipping_amount ");
        DB::statement("ALTER TABLE orders MODIFY COLUMN updated_at  DATE AFTER shipping_amount ");
        DB::statement("ALTER TABLE orders MODIFY COLUMN created_at  DATE AFTER shipping_amount ");
        Schema::table('orders_details', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};