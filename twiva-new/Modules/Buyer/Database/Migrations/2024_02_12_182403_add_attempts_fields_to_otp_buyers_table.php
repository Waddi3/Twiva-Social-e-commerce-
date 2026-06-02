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
        Schema::table('buyers_otp', function (Blueprint $table) {
            $table->integer('attempts')->default(0)->after('code');
            $table->dateTime('last_attempt')->nullable()->after('attempts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buyers_otp', function (Blueprint $table) {
            $table->dropColumn(['attempts', 'last_attempt']);
        });
    }
};
