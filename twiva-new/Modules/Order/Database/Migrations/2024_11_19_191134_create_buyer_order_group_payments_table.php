<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyer_order_group_payments', function (Blueprint $table)
        {
            $table->id();
            $table->string('buyer_id');
            $table->string('request_id');
            $table->string('amount');
            $table->string('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_order_group_payments');
    }
};
