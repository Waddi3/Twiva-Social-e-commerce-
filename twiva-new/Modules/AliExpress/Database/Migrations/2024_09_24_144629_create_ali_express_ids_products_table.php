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
        Schema::create('ali_express_ids_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->index()->unique();
            $table->string('title', 1000);
            $table->string('category_ids');
            $table->string('score');
            $table->string('item_url');
            $table->string('item_main_pic');
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
        Schema::dropIfExists('ali_express_ids_products');
    }
};
