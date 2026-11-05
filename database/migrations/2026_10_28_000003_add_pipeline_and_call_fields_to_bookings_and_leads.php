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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'pipeline_stage')) {
                $table->string('pipeline_stage', 64)->default('initial_contact');
            }
            if (!Schema::hasColumn('bookings', 'call_confirmed')) {
                $table->boolean('call_confirmed')->default(false);
            }
            if (!Schema::hasColumn('bookings', 'call_confirmed_at')) {
                $table->timestamp('call_confirmed_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'required_hardware_amount')) {
                $table->decimal('required_hardware_amount', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('bookings', 'chat_transcript')) {
                $table->longText('chat_transcript')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'visit_started_at')) {
                $table->timestamp('visit_started_at')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'actual_hours')) {
                $table->integer('actual_hours')->nullable();
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'pipeline_stage')) {
                $table->string('pipeline_stage', 64)->default('initial_contact');
            }
            if (!Schema::hasColumn('leads', 'call_confirmed')) {
                $table->boolean('call_confirmed')->default(false);
            }
            if (!Schema::hasColumn('leads', 'call_confirmed_at')) {
                $table->timestamp('call_confirmed_at')->nullable();
            }
            if (!Schema::hasColumn('leads', 'chat_transcript')) {
                $table->longText('chat_transcript')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('bookings', 'pipeline_stage') ? 'pipeline_stage' : null,
                Schema::hasColumn('bookings', 'call_confirmed') ? 'call_confirmed' : null,
                Schema::hasColumn('bookings', 'call_confirmed_at') ? 'call_confirmed_at' : null,
                Schema::hasColumn('bookings', 'required_hardware_amount') ? 'required_hardware_amount' : null,
                Schema::hasColumn('bookings', 'chat_transcript') ? 'chat_transcript' : null,
                Schema::hasColumn('bookings', 'visit_started_at') ? 'visit_started_at' : null,
                Schema::hasColumn('bookings', 'actual_hours') ? 'actual_hours' : null,
            ]));
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('leads', 'pipeline_stage') ? 'pipeline_stage' : null,
                Schema::hasColumn('leads', 'call_confirmed') ? 'call_confirmed' : null,
                Schema::hasColumn('leads', 'call_confirmed_at') ? 'call_confirmed_at' : null,
                Schema::hasColumn('leads', 'chat_transcript') ? 'chat_transcript' : null,
            ]));
        });
    }
};
