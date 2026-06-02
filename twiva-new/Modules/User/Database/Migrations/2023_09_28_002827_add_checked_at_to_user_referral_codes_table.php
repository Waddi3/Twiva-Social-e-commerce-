<?php

use Illuminate\Support\Facades\DB;
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
        Schema::table('user_referral_codes', function (Blueprint $table) {
            $table->timestamp('checked_at')->nullable()->after('is_activated');
        });

        DB::statement("UPDATE user_referral_codes SET checked_at = NOW()");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_referral_codes', function (Blueprint $table) {
            $table->dropColumn('checked_at');
        });
    }
};
