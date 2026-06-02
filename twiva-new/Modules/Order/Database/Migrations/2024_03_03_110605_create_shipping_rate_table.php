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
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('source_city_id');
            $table->foreign('source_city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->unsignedMediumInteger('destination_city_id');
            $table->foreign('destination_city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->decimal('rate', 14, 3)->default(0);
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
        Schema::dropIfExists('shipping_rates');
    }
};
