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
            $table->index('user_id');
            $table->index('category');
            $table->index('sub_category');
            $table->index('name');
            $table->index('sku');
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
            $table->dropIndex('products_user_id_index');
            $table->dropIndex('products_category_index');
            $table->dropIndex('products_sub_category_index');
            $table->dropIndex('products_name_index');
            $table->dropIndex('products_sku_index');
        });
    }
};
