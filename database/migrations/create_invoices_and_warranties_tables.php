<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Invoices and Warranties ecosystem.
     */
    public function up(): void
    {
        // 1. Invoices Table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // e.g. INV-261103-A1B2C
            $table->string('hash_id', 64)->unique()->index(); // for public QR verification /invoice/verify/{hash_id}
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email')->nullable();
            $table->string('client_company')->nullable();
            $table->text('client_address')->nullable();
            
            // Financials
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00); // VAT if applicable
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->string('currency', 10)->default('EGP');
            
            // Payment details
            $table->enum('payment_method', ['cash', 'wallet', 'card', 'bank_transfer', 'fawry', 'vodafone_cash'])->default('cash');
            $table->enum('payment_status', ['unpaid', 'paid', 'partially_paid', 'refunded'])->default('paid');
            $table->timestamp('paid_at')->nullable();
            
            // Sector origin
            $table->enum('source_type', ['field_service', 'hardware_order', 'erp_license', 'custom'])->default('field_service');
            $table->unsignedBigInteger('source_id')->nullable(); // booking_id, hardware_order_id, erp_order_id
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Invoice Line Items Table
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('item_name'); // Service name, hardware part, or ERP module
            $table->string('item_code')->nullable(); // SKU, Serial number, or License key
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->default(0.00);
            
            // Warranty metadata attached to this item
            $table->integer('warranty_days')->default(0); // 0 = no warranty
            $table->string('warranty_policy')->nullable(); // replacement / repair
            
            $table->timestamps();
        });

        // 3. Digital Warranties Table
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique(); // e.g. WAR-2611-X9Y2
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->nullable()->constrained('invoice_items')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('product_or_service_name');
            $table->string('serial_number')->nullable()->index();
            $table->enum('coverage_type', ['replacement', 'repair', 'software_maintenance'])->default('repair');
            
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->enum('status', ['active', 'claimed', 'expired', 'voided'])->default('active');
            
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
        });

        // 4. Warranty Claims (Free maintenance requests by clients)
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->foreignId('warranty_id')->constrained('warranties')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            
            $table->text('issue_description');
            $table->json('attachments')->nullable(); // photo/video proof
            $table->enum('claim_status', ['pending', 'approved', 'rejected', 'in_progress', 'resolved'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_claims');
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
