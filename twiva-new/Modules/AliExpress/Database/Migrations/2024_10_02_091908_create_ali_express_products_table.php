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
        Schema::create('ali_express_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->unique()->index();
            $table->string('first_level_category_id');
            $table->string('second_level_category_id');
            $table->string('title', 500);
            $table->string('product_main_image_url', 500);
            $table->json('product_small_image_url');
            $table->json('product_info');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ali_express_products');
    }
};
