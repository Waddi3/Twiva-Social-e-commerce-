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
        Schema::table('buyers', function (Blueprint $table) {
            $table->string('class', 20)->nullable()->after('phone_number')->comment('INDIVIDUAL | RETAILER');
            $table->string('rep_name', 30)->nullable()->after('class')->comment('when type is RETAILER');
            $table->string('rep_phone_code', 30)->nullable()->after('rep_name')->comment('when type is RETAILER');
            $table->string('rep_phone_number', 30)->nullable()->after('rep_phone_code')->comment('when type is RETAILER');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('buyers', function (Blueprint $table) {
            $table->dropColumn([
                'class',
                'rep_name',
                'rep_phone_code',
                'rep_phone_number',
            ]);
        });
    }
};
