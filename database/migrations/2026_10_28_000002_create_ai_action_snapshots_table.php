<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_action_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('action_type', 32); // create, update, delete
            $table->string('target_table', 64);
            $table->unsignedBigInteger('target_id');
            $table->longText('before_state_json')->nullable();
            $table->longText('after_state_json')->nullable();
            $table->string('description_ar', 500);
            $table->boolean('is_reverted')->default(false);
            $table->timestamps();

            $table->index(['target_table', 'target_id']);
            $table->index('is_reverted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_action_snapshots');
    }
};
