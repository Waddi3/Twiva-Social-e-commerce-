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
        Schema::create('brand_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('voice')->nullable();
            $table->string('tone')->nullable();
            $table->string('style')->nullable();
            $table->string('structure')->nullable();
            $table->string('location')->nullable();
            $table->unsignedMediumInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('latitude', 10, 8)->nullable(); // -1.454492972940748
            $table->double('longitude', 11, 8)->nullable(); // 36.87834928336266
            $table->tinyInteger('min_age')->nullable();
            $table->tinyInteger('max_age')->nullable();
            $table->enum('gender', ['all', 'male', 'female'])->default('all');
            $table->string('language_ids')->nullable();
            $table->string('interest_ids')->nullable();
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
        Schema::dropIfExists('brand_themes');
    }
};
