<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->string('uuid')->unique();
        });
    }

    public function down()
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            DB::table('failed_jobs')->truncate();
            $table->string('uuid')->after('id')->unique();
        });
    }
};