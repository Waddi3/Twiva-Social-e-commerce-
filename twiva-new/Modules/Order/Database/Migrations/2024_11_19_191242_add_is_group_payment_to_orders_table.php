<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table)
        {
            if (in_array('payment_type', Schema::getColumnListing('orders')))
            {
                $table->dropColumn('payment_type');
            }
            $table->foreignId('group_payment_request_id')
                ->nullable()
                ->references('id')
                ->on('order_group_payment_requests');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table)
        {
            $table->dropConstrainedForeignId('group_payment_request_id');
        });
    }
};
