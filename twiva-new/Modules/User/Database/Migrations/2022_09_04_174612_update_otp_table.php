<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('otp', function (Blueprint $table) {
            $table->dropIfExists('otp');

        });
        Schema::create('otp', function (Blueprint $table) {
            $table->id();
            $table->integer('otp');
            $table->enum('type', ['MOBILE', 'EMAIL']);
            $table->timestamp('expiry_date');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->timestamps();
        });

    }

    public function down()
    {

        Schema::table('otp', function (Blueprint $table) {
            $table->dropIfExists('otp');

        });
    }
};