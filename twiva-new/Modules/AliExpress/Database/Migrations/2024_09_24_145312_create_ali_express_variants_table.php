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
        Schema::create('ali_express_variants', function (Blueprint $table) {
            $table->id();
            $table->string('sku_id')->index();
            $table->string('sku_attr')->index();
            $table->json('ae_item_sku_info_d_t_o');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('ali_express_product_id')->index();
            // $table->foreign('ali_express_product_id')
            //     ->references('product_id')
            //     ->on('ali_express_ids_products')
            //     ->cascadeOnDelete()
            //     ->cascadeOnUpdate();
            $table->string('sku_price')->nullable();
            $table->string('offer_sale_price')->nullable();
            $table->string('offer_bulk_sale_price')->nullable();
            $table->string('min_shipping_fee_cent')->nullable();
            $table->string('max_shipping_fee_cent')->nullable();
            $table->string('min_delivery_day')->nullable();
            $table->string('max_delivery_day')->nullable();
            $table->json('delivery_option')->nullable();
            $table->timestamps();

            $table->unique([
                'sku_id',
                'sku_attr',
                'ali_express_product_id',
            ], 'sku_product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ali_express_variants');
    }
};
