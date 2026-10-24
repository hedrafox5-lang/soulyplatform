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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->text('short_description');
            $table->string('hero_badge')->nullable();
            $table->string('icon_key', 50)->nullable();
            $table->string('accent_color', 20)->default('#00F5D4');
            $table->string('secondary_color', 20)->default('#FFB800');
            $table->json('hero_slides_json')->nullable();
            $table->json('stats_json')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('row_key', 80);
            $table->string('row_type', 60); // hero_highlight, before_after, icon_matrix_industries, icon_matrix_features, packages_showcase, process_steps, curriculum_preview, custom_cta
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->json('payload_json')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_icons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('group_type', 60); // industry, feature, threat, protection_tech, problem_solution, consulting_track, course_skill
            $table->string('title');
            $table->string('icon_name', 80);
            $table->string('badge_text')->nullable();
            $table->string('popup_title');
            $table->text('popup_summary');
            $table->json('popup_bullets_json')->nullable();
            $table->string('popup_cta_text')->default('احجز هذه الميزة الآن');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('slug', 80)->index();
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('billing_type', 30); // one_time, hourly, monthly, yearly, course, custom
            $table->decimal('original_price', 12, 2)->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('min_units')->default(1);
            $table->string('unit_label', 50)->nullable();
            $table->integer('warranty_days')->nullable();
            $table->json('features_json');
            $table->json('subpage_sections_json')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['service_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_packages');
        Schema::dropIfExists('service_icons');
        Schema::dropIfExists('service_rows');
        Schema::dropIfExists('services');
    }
};
