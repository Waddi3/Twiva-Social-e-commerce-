<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        Schema::table('comp_details', function (Blueprint $table) {
            $table->dropColumn('last_cron_run_date');
            $table->dropColumn('comp_email');
            $table->dropColumn('country_code');
            $table->dropColumn('logo');
            $table->dropColumn('pincode');
            $table->dropColumn('busi_name');
            $table->dropColumn('building');
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('comp_details', function (Blueprint $table) {
            $table->string('comp_email')->nullable();
            $table->string('country_code')->nullable();
            $table->string('logo')->nullable();
            $table->string('pincode')->nullable();
            $table->string('busi_name')->nullable();
            $table->string('building')->nullable();
        });
    }
};