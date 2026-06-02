<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('shipping_address', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropColumn('postal_code');
            $table->dropColumn('apartment');
            $table->renameColumn('locality', 'delivery_address');
            $table->renameColumn('state', 'county_id');
            $table->renameColumn('country', 'country_id');
            $table->renameColumn('city', 'city_id');
            $table->string('email')->nullable()->change();

        });
    }

    public function down()
    {
        Schema::table('shipping_address', function (Blueprint $table) {
            //
        });
    }
};