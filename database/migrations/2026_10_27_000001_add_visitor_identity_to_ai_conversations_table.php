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
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->string('visitor_uuid', 64)->nullable()->index()->after('session_uuid');
            $table->string('visitor_name', 100)->nullable()->after('visitor_uuid');
            $table->string('device_fingerprint', 100)->nullable()->after('visitor_name');
            $table->text('summary')->nullable()->after('detected_intent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->dropIndex(['visitor_uuid']);
            $table->dropColumn(['visitor_uuid', 'visitor_name', 'device_fingerprint', 'summary']);
        });
    }
};
