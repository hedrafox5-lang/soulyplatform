<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Promo Codes and Usages.
     */
    public function up(): void
    {
        if (!Schema::hasTable('promo_codes')) {
            Schema::create('promo_codes', function (Blueprint $table) {
                $table->id();
                $table->string('code', 30)->unique();
                $table->enum('type', ['fixed', 'percentage', 'bonus_balance'])->default('fixed');
                $table->decimal('value', 12, 2)->default(0.00);
                $table->decimal('min_order_amount', 12, 2)->nullable();
                $table->decimal('max_discount_cap', 12, 2)->nullable();
                $table->integer('max_uses_total')->nullable();
                $table->integer('max_uses_per_user')->default(1);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('promo_code_usages')) {
            Schema::create('promo_code_usages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('order_type')->nullable(); // e.g. booking, hardware, erp
                $table->unsignedBigInteger('order_id')->nullable();
                $table->decimal('discount_amount', 12, 2)->default(0.00);
                $table->timestamp('used_at')->useCurrent();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_code_usages');
        Schema::dropIfExists('promo_codes');
    }
};