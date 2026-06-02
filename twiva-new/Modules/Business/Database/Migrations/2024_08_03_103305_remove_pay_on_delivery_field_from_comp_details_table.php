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
        Schema::table('comp_details', function (Blueprint $table) {
            $table->dropColumn('pay_on_delivery');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comp_details', function (Blueprint $table) {
            $table->tinyInteger('pay_on_delivery')->default(1)->after('area');
        });
    }
};
