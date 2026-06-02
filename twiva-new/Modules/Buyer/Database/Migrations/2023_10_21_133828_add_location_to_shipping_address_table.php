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
        Schema::table('shipping_address', function (Blueprint $table) {
            $table->double('latitude', 10, 8)->nullable()->after('email'); // -1.454492972940748
            $table->double('longitude', 11, 8)->nullable()->after('latitude'); // 36.87834928336266
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_address', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
            ]);
        });
    }
};
