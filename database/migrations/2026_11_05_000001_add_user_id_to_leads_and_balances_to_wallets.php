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
        if (Schema::hasTable('leads') && !Schema::hasColumn('leads', 'user_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('assigned_to');
            });
        }

        if (Schema::hasTable('wallets')) {
            Schema::table('wallets', function (Blueprint $table) {
                if (!Schema::hasColumn('wallets', 'cash_balance')) {
                    $table->decimal('cash_balance', 14, 2)->default(0.00)->after('balance');
                }
                if (!Schema::hasColumn('wallets', 'bonus_balance')) {
                    $table->decimal('bonus_balance', 14, 2)->default(0.00)->after('cash_balance');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'user_id')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasTable('wallets')) {
            Schema::table('wallets', function (Blueprint $table) {
                if (Schema::hasColumn('wallets', 'bonus_balance')) {
                    $table->dropColumn('bonus_balance');
                }
                if (Schema::hasColumn('wallets', 'cash_balance')) {
                    $table->dropColumn('cash_balance');
                }
            });
        }
    }
};
