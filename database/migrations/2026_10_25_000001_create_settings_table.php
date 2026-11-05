<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general');
            $table->string('type')->default('string')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('group');
        });

        // Seed Default Initial Settings
        $now = now();
        $defaults = [
            // General & Contact
            ['key' => 'site_name', 'value' => 'سولي', 'group' => 'general', 'type' => 'string', 'description' => 'اسم المنظومة الرسمي'],
            ['key' => 'contact_phone', 'value' => '01090623326', 'group' => 'contact', 'type' => 'string', 'description' => 'رقم هاتف الاتصال الرئيسي'],
            ['key' => 'contact_whatsapp', 'value' => '01090623326', 'group' => 'contact', 'type' => 'string', 'description' => 'رقم واتساب المعتمد'],
            ['key' => 'contact_email', 'value' => 'support@soulyeg.online', 'group' => 'contact', 'type' => 'string', 'description' => 'البريد الإلكتروني للدعم الفني'],
            ['key' => 'vodafone_cash_number', 'value' => '01090623326', 'group' => 'payment', 'type' => 'string', 'description' => 'رقم فودافون كاش لتحويل الإيداعات'],
            ['key' => 'instapay_address', 'value' => 'souly@instapay', 'group' => 'payment', 'type' => 'string', 'description' => 'عنوان حساب إنستاباي المعتمد'],
            ['key' => 'bank_name', 'value' => 'البنك الأهلي المصري (NBE)', 'group' => 'payment', 'type' => 'string', 'description' => 'اسم البنك للتحويلات المباشرة'],
            ['key' => 'bank_iban', 'value' => 'EG3800020000000109062332601', 'group' => 'payment', 'type' => 'string', 'description' => 'رقم الآيبان IBAN'],
            ['key' => 'bank_account_name', 'value' => 'مؤسسة سولي للحلول الرقمية', 'group' => 'payment', 'type' => 'string', 'description' => 'اسم صاحب الحساب البنكي'],

            // AI & Vector Store
            ['key' => 'pinecone_api_key', 'value' => env('PINECONE_API_KEY', ''), 'group' => 'ai', 'type' => 'string', 'description' => 'مفتاح Pinecone API Key'],
            ['key' => 'pinecone_host', 'value' => env('PINECONE_HOST', ''), 'group' => 'ai', 'type' => 'string', 'description' => 'نطاق استضافة Pinecone Host'],
            ['key' => 'pinecone_index', 'value' => env('PINECONE_INDEX', 'souly-knowledge-v1'), 'group' => 'ai', 'type' => 'string', 'description' => 'اسم فهرس Pinecone Index'],
            ['key' => 'gemini_api_key', 'value' => env('GEMINI_API_KEY', ''), 'group' => 'ai', 'type' => 'string', 'description' => 'مفتاح Google Gemini API Key'],
            ['key' => 'openai_api_key', 'value' => env('OPENAI_API_KEY', ''), 'group' => 'ai', 'type' => 'string', 'description' => 'مفتاح OpenAI API Key'],
            ['key' => 'ai_welcome_message', 'value' => 'مرحباً بك في سولي! 🛡️ أنا مستشارك الذكي، كيف يمكنني مساعدتك في تطوير أعمالك أو حجز خدماتك اليوم؟', 'group' => 'ai', 'type' => 'text', 'description' => 'رسالة الترحيب بمساعد الذكاء الاصطناعي'],
            ['key' => 'pinecone_last_synced_at', 'value' => null, 'group' => 'ai', 'type' => 'string', 'description' => 'تاريخ آخر مزامنة مع Pinecone'],

            // Cinematic Experience
            ['key' => 'enable_cinematic_loader', 'value' => '1', 'group' => 'cinematic', 'type' => 'boolean', 'description' => 'تفعيل شاشة التحميل السينمائية للموقع'],
            ['key' => 'loader_duration_seconds', 'value' => '5', 'group' => 'cinematic', 'type' => 'integer', 'description' => 'مدة لودر البداية بالثواني'],
            ['key' => 'enable_cinematic_audio', 'value' => '1', 'group' => 'cinematic', 'type' => 'boolean', 'description' => 'تشغيل المؤثر الصوتي الهادئ مع اللودر'],
        ];

        foreach ($defaults as $item) {
            DB::table('settings')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
