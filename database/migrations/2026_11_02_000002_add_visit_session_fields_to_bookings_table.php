<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'visit_started_at')) {
                $table->timestamp('visit_started_at')->nullable()->after('scheduled_at');
            }
            if (!Schema::hasColumn('bookings', 'visit_ended_at')) {
                $table->timestamp('visit_ended_at')->nullable()->after('visit_started_at');
            }
            if (!Schema::hasColumn('bookings', 'billed_hours')) {
                $table->decimal('billed_hours', 5, 2)->default(3.00);
            }
            if (!Schema::hasColumn('bookings', 'hourly_rate')) {
                $table->decimal('hourly_rate', 8, 2)->default(400.00);
            }
            if (!Schema::hasColumn('bookings', 'labor_amount')) {
                $table->decimal('labor_amount', 10, 2)->default(1200.00);
            }
            if (!Schema::hasColumn('bookings', 'hardware_amount')) {
                $table->decimal('hardware_amount', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('bookings', 'hardware_parts_json')) {
                $table->json('hardware_parts_json')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'technician_rating')) {
                $table->unsignedTinyInteger('technician_rating')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'client_review')) {
                $table->text('client_review')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'visit_started_at',
                'visit_ended_at',
                'billed_hours',
                'hourly_rate',
                'labor_amount',
                'hardware_amount',
                'hardware_parts_json',
                'technician_rating',
                'client_review',
            ]);
        });
    }
};