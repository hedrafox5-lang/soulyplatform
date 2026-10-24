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
        Schema::create('canned_response_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 80)->unique();
            $table->string('icon', 50)->default('chat');
            $table->string('accent_color', 20)->default('#00F5D4');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('canned_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('canned_response_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('shortcut_code', 50)->nullable()->index();
            $table->text('content');
            $table->json('tags_json')->nullable();
            $table->integer('usage_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canned_responses');
        Schema::dropIfExists('canned_response_categories');
    }
};
