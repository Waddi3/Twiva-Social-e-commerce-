<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumns('orders', ['business_id', 'shipping_amount']))
        {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('business_id')->nullable()->after('influencer_id')->references('id')->on('users');
                $table->float('shipping_amount')->after('order_amount')->default(0)->nullable();
            });
        }
    }


    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};