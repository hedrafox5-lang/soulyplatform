<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('model_number');
            $table->text('details');
            $table->json('images_json')->nullable();
            $table->string('status')->default('needs_maintenance'); // healthy, needs_maintenance, visit_requested, in_company_repair, warranty_active
            $table->string('last_maintenance_type')->nullable(); // on_site_visit, in_company
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_devices');
    }
};