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
        Schema::table('coupons', function (Blueprint $table) {
            $table->foreignId('buyer_id')->after('is_active')->nullable()->constrained('buyers')->cascadeOnDelete();
            $table->boolean('by_spin_wheel')->nullable()->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
            $table->dropColumn(['by_spin_wheel', 'buyer_id']);
        });
    }
};
