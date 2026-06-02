<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $listTableIndexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('orders');
            if (isset($listTableIndexes['orders_buyer_id_foreign']))
            {
                $table->dropForeign('orders_buyer_id_foreign');
            }
            $table->unsignedBigInteger('buyer_id')->nullable()->change();
        });
        Schema::table('shipping_address', function (Blueprint $table) {
            $listTableIndexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('shipping_address');
            if (isset($listTableIndexes['shipping_address_buyer_id_foreign']))
            {
                $table->dropForeign('shipping_address_buyer_id_foreign');
            }
            $table->unsignedBigInteger('buyer_id')->nullable()->change();
        });
        Schema::table('buyer_details', function (Blueprint $table) {
            $listTableIndexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('buyer_details');
            if (isset($listTableIndexes['buyer_details_buyer_id_foreign']))
            {
                $table->dropForeign('buyer_details_buyer_id_foreign');
            }
        });

    }


};