<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shipping_address', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->dropColumn('order_id');
        });

        Schema::table('orders_details', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('address_id')->references('id')->on('shipping_address');
            $table->dropColumn('transaction_details');
        });
        Schema::table('busi_mpesa', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('mobileNumber')->nullable()->change();
        });

    }

    public function down()
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
};