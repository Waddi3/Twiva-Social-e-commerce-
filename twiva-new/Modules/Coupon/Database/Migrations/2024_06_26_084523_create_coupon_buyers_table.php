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
        Schema::create('coupon_buyers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type', 15);
            $table->string('amount', 15);
            // Add a unique constraint to ensure a user can't use the same coupon multiple times across different orders
            $table->unique(['coupon_id', 'buyer_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_buyers');
    }
};
