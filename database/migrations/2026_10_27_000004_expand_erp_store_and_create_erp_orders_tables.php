<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Expand erp_store_catalog with rich marketplace columns
        Schema::table('erp_store_catalog', function (Blueprint $table) {
            if (!Schema::hasColumn('erp_store_catalog', 'code')) {
                $table->string('code', 50)->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('erp_store_catalog', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (!Schema::hasColumn('erp_store_catalog', 'department_ar')) {
                $table->string('department_ar', 100)->nullable()->after('department');
            }
            if (!Schema::hasColumn('erp_store_catalog', 'compatible_industries')) {
                $table->json('compatible_industries')->nullable()->after('department_ar');
            }
            if (!Schema::hasColumn('erp_store_catalog', 'icon_svg')) {
                $table->text('icon_svg')->nullable()->after('icon_name');
            }
            if (!Schema::hasColumn('erp_store_catalog', 'short_desc_ar')) {
                $table->text('short_desc_ar')->nullable()->after('description');
            }
            if (!Schema::hasColumn('erp_store_catalog', 'full_details_ar')) {
                $table->text('full_details_ar')->nullable()->after('short_desc_ar');
            }
            if (!Schema::hasColumn('erp_store_catalog', 'roi_time_saved_ar')) {
                $table->string('roi_time_saved_ar', 120)->nullable()->after('full_details_ar');
            }
            if (!Schema::hasColumn('erp_store_catalog', 'activations_count')) {
                $table->unsignedInteger('activations_count')->default(0)->after('roi_time_saved_ar');
            }
            if (!Schema::hasColumn('erp_store_catalog', 'is_flash_deal')) {
                $table->boolean('is_flash_deal')->default(false)->after('activations_count')->index();
            }
            if (!Schema::hasColumn('erp_store_catalog', 'is_sales_engineer_pick')) {
                $table->boolean('is_sales_engineer_pick')->default(false)->after('is_flash_deal')->index();
            }
            if (!Schema::hasColumn('erp_store_catalog', 'related_hardware_slug')) {
                $table->string('related_hardware_slug', 100)->nullable()->after('is_sales_engineer_pick');
            }
        });

        // 2. Customer ERP Orders & Licenses
        if (!Schema::hasTable('erp_orders')) {
            Schema::create('erp_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('order_number', 50)->unique();
                $table->string('customer_name')->nullable();
                $table->string('customer_phone', 40)->nullable();
                $table->string('customer_email')->nullable();
                $table->json('items_json'); // array of ordered item codes/slugs
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('payment_method', 50)->default('wallet'); // wallet, direct_transfer, cash, card
                $table->enum('status', ['pending', 'active', 'cancelled'])->default('pending')->index();
                $table->string('license_key', 80)->nullable()->unique();
                $table->boolean('is_trial')->default(false);
                $table->timestamp('trial_expires_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Order Items Breakdown
        if (!Schema::hasTable('erp_order_items')) {
            Schema::create('erp_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('erp_orders')->cascadeOnDelete();
                $table->foreignId('catalog_item_id')->nullable()->constrained('erp_store_catalog')->nullOnDelete();
                $table->string('item_code', 50)->nullable();
                $table->string('item_name');
                $table->string('tier', 30);
                $table->decimal('price', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // 4. Client ERP Wishlists
        if (!Schema::hasTable('erp_wishlists')) {
            Schema::create('erp_wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('catalog_item_id')->constrained('erp_store_catalog')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'catalog_item_id']);
            });
        }

        // 5. Custom Feature Requests
        if (!Schema::hasTable('erp_custom_requests')) {
            Schema::create('erp_custom_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('customer_name')->nullable();
                $table->string('customer_phone', 40)->nullable();
                $table->string('industry', 80)->nullable();
                $table->string('feature_title');
                $table->text('feature_description');
                $table->string('budget_range', 80)->nullable();
                $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending')->index();
                $table->text('admin_response')->nullable();
                $table->timestamps();
            });
        }

        // 6. Sales Engineer Proposals to Specific Clients
        if (!Schema::hasTable('erp_sales_proposals')) {
            Schema::create('erp_sales_proposals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->text('notes')->nullable();
                $table->json('proposed_items_json'); // array of catalog slugs/codes
                $table->decimal('discount_percent', 5, 2)->default(0);
                $table->decimal('total_before_discount', 12, 2)->default(0);
                $table->decimal('total_after_discount', 12, 2)->default(0);
                $table->enum('status', ['sent', 'accepted', 'rejected'])->default('sent')->index();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_sales_proposals');
        Schema::dropIfExists('erp_custom_requests');
        Schema::dropIfExists('erp_wishlists');
        Schema::dropIfExists('erp_order_items');
        Schema::dropIfExists('erp_orders');

        Schema::table('erp_store_catalog', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'name_ar',
                'department_ar',
                'compatible_industries',
                'icon_svg',
                'short_desc_ar',
                'full_details_ar',
                'roi_time_saved_ar',
                'activations_count',
                'is_flash_deal',
                'is_sales_engineer_pick',
                'related_hardware_slug',
            ]);
        });
    }
};
