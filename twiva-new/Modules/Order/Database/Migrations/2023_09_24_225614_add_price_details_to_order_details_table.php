<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('product_price')->nullable()->after('amount');
            $table->string('product_retail_price')->nullable()->after('product_price');
            $table->string('product_discount_price')->nullable()->after('product_retail_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('product_price');
            $table->dropColumn('product_retail_price');
            $table->dropColumn('product_discount_price');
        });
    }
};
