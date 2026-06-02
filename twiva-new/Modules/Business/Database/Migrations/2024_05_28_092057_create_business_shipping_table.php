<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_shippings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->on('users')->references('id');

            $table->integer('shipping_by_partner')->nullable();

            $table->tinyInteger('has_shipping_fees')->default(1);
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
        Schema::dropIfExists('business_shippings');
    }
};
