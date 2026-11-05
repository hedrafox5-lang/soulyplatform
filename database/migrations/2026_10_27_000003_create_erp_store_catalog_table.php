<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_store_catalog', function (Blueprint $table) {
            $table->id();
            $table->enum('tier', ['core', 'module', 'feature', 'bundle']); // tier 1-4
            $table->string('name');           // Arabic name
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('department', 100)->nullable(); // for features: المبيعات, المخازن, الموارد البشرية, etc.
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('original_price', 10, 2)->nullable(); // for discount display
            $table->string('badge', 80)->nullable(); // الأكثر مبيعاً, جديد, عرض خاص, موصى به
            $table->string('icon_name', 80)->nullable(); // heroicon name or emoji
            $table->json('features_json')->nullable(); // list of bullet points
            $table->json('included_items')->nullable(); // for bundles: list of slugs
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_store_catalog');
    }
};
