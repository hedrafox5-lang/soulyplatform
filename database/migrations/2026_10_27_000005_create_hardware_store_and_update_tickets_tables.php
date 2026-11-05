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
        // 1. Hardware Products Catalog Table
        if (!Schema::hasTable('hardware_products')) {
            Schema::create('hardware_products', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 120)->unique();
                $table->string('sku', 60)->unique();
                $table->string('name_ar', 180);
                $table->string('category', 60); // pos_equipment, storage_ram, networking_security, workstations
                $table->string('brand', 100);
                $table->decimal('price', 10, 2);
                $table->decimal('old_price', 10, 2)->nullable();
                $table->integer('stock_qty')->default(10);
                $table->integer('warranty_months')->default(12);
                $table->json('specs_json')->nullable();
                $table->text('image_svg_or_url')->nullable();
                $table->text('description_ar')->nullable();
                $table->boolean('compatible_with_branderp')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Hardware Customer Orders Table
        if (!Schema::hasTable('hardware_orders')) {
            Schema::create('hardware_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number', 60)->unique();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('customer_name', 120);
                $table->string('customer_phone', 40);
                $table->text('shipping_address');
                $table->json('items_json');
                $table->decimal('total_amount', 10, 2);
                $table->string('payment_method', 40)->default('transfer'); // wallet, transfer, cod_deposit
                $table->string('payment_status', 40)->default('pending'); // pending, paid, refunded
                $table->string('status', 50)->default('pending_review'); // pending_review, confirmed_preparing, out_for_delivery, delivered_warranty_active, cancelled
                $table->text('serial_numbers_notes')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Hardware Order Items Table
        if (!Schema::hasTable('hardware_order_items')) {
            Schema::create('hardware_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('hardware_orders')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('hardware_products')->nullOnDelete();
                $table->string('product_sku', 60);
                $table->string('product_name', 180);
                $table->decimal('unit_price', 10, 2);
                $table->integer('quantity')->default(1);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();
            });
        }

        // 4. Update Tickets Table with Multi-Store Scope & Reference Code
        if (Schema::hasTable('tickets')) {
            Schema::table('tickets', function (Blueprint $table) {
                if (!Schema::hasColumn('tickets', 'department_scope')) {
                    $table->string('department_scope', 50)->default('general')->after('department');
                }
                if (!Schema::hasColumn('tickets', 'reference_code')) {
                    $table->string('reference_code', 80)->nullable()->after('department_scope');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tickets')) {
            Schema::table('tickets', function (Blueprint $table) {
                if (Schema::hasColumn('tickets', 'reference_code')) {
                    $table->dropColumn('reference_code');
                }
                if (Schema::hasColumn('tickets', 'department_scope')) {
                    $table->dropColumn('department_scope');
                }
            });
        }

        Schema::dropIfExists('hardware_order_items');
        Schema::dropIfExists('hardware_orders');
        Schema::dropIfExists('hardware_products');
    }
};
