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
        Schema::create('category_whats_app_groups', function (Blueprint $table) {
            $table->id();
            $table->morphs('categorizable', 'category_whats_app_groups_categorizable_index');
            $table->foreignId('whats_app_group_id')->constrained('whats_app_groups')->cascadeOnDelete();
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

        Schema::table('category_whats_app_groups', function (Blueprint $table) {
            $table->dropIndex(['category_whats_app_groups_categorizable_index']);
        });
        Schema::dropIfExists('category_whats_app_groups');
    }
};
