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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // Index for faster searching by name
            $table->string('code')->unique(); // Unique and indexed for searching by code
            $table->unsignedSmallInteger('max_usage')->nullable(); // Optional field for maximum usage
            // $table->string('type')->default('fixed'); // fixed | percentage
            $table->enum('type', ['fixed', 'percentage'])->default('fixed'); // Enum type for fixed or percentage
            // $table->string('amount');
            $table->decimal('amount', 10, 2); // Decimal for precise amount handling
            $table->timestamp('expiry_at')->nullable(); // Timestamp for expiration date, nullable in case of no expiry
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};
