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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 50)->index();
            $table->string('email')->nullable();
            $table->string('company_name')->nullable();
            $table->string('business_activity')->nullable();
            $table->foreignId('interested_service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('interested_package_id')->nullable()->constrained('service_packages')->nullOnDelete();
            $table->string('source', 40)->default('manual'); // ai_agent, website_booking, contact_form, whatsapp, facebook_ads, manual
            $table->string('status', 30)->default('new'); // new, contacted, qualified, proposal_sent, won, lost
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ai_summary')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 40); // note, call, whatsapp, status_change, ai_capture
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
    }
};
