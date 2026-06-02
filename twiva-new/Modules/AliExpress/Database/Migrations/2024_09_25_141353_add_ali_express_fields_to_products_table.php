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
        Schema::table('products', function (Blueprint $table) {
            $table->string('ali_express_product_id')->nullable()->unique();
            $table->string('ali_express_price')->nullable();
            $table->string('ali_express_discount')->nullable();
            $table->string('ali_express_sales_count')->nullable();
            $table->boolean('is_ali_express')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('ali_express_product_id');
            $table->dropColumn(['is_ali_express', 'ali_express_product_id', 'ali_express_discount', 'ali_express_sales_count']);
        });
    }
};
