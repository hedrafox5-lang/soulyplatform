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
        Schema::create('ai_api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('label')->default('Gemini Key');
            $table->string('account_email')->nullable();
            $table->string('provider', 32)->default('gemini'); // gemini, pinecone
            $table->text('api_key');
            $table->string('scope', 32)->default('all'); // all, admin_only, visitor_only
            $table->integer('priority')->default(1);
            $table->string('status', 32)->default('active'); // active, cooldown, invalid, disabled
            $table->timestamp('cooldown_until')->nullable();
            $table->integer('requests_today')->default(0);
            $table->integer('total_requests')->default(0);
            $table->integer('last_latency_ms')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['provider', 'status', 'priority']);
            $table->index('account_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_api_keys');
    }
};
