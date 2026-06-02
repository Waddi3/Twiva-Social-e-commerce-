<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_group_payment_requests', function (Blueprint $table)
        {
            $table->id();
            $table->double('amount');
            $table->json('cart');
            $table->string('uuid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_group_payment_requests');
    }
};
