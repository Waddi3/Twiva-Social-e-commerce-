<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\System\Entities\MobileVersion;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_versions', function (Blueprint $table) {
            $table->id();
            $table->string('app_name', 50)->index();
            $table->string('platform', 20)->index();
            $table->string('version', 30)->index();
            $table->boolean('is_forced')->default(false);
            $table->string('link')->nullable();
            $table->string('store_link')->nullable();
            $table->text('features')->nullable();
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
        Schema::dropIfExists('mobile_versions');
    }
};
