<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_ticket_products', function (Blueprint $table)
        {
            $table->id();
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->string('ticket_number')->unique()->nullable();
            $table->foreignId('business_product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_ticket_products');
    }
};
