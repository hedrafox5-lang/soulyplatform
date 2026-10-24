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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('service_package_id')->nullable()->constrained('service_packages')->nullOnDelete();
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('quantity_or_hours')->default(1);
            $table->decimal('total_amount', 12, 2);
            $table->string('payment_method', 40); // wallet, direct_transfer, on_site
            $table->string('payment_status', 30)->default('unpaid'); // unpaid, pending_verification, paid, refunded
            $table->string('status', 30)->default('new'); // new, confirmed, in_progress, completed, cancelled
            $table->timestamp('scheduled_at')->nullable();
            $table->text('client_requirements')->nullable();
            $table->text('staff_report')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
