<?php

namespace Database\Seeders;

use App\Models\AiApiKey;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class AiApiKeysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keys = [
            [
                'label' => 'Gemini Primary Core (Hedra Adel)',
                'account_email' => 'hedra.adel@gmail.com',
                'provider' => 'gemini',
                'api_key' => 'AQ.Ab8RN6LuAZtSCNrgqDM-M77fxHlUb3y7Lanot0smOvbDQ0tKBQ',
                'scope' => 'all',
                'priority' => 1,
                'status' => 'active',
            ],
            [
                'label' => 'Gemini Secondary Backup (Hedra Fox)',
                'account_email' => 'hedrafox5@gmail.com',
                'provider' => 'gemini',
                'api_key' => 'AQ.Ab8RN6JcoVWqRnqO8Xyoqzq-tIq6nKI0POvmrdEA4FNu9eVuYA',
                'scope' => 'all',
                'priority' => 2,
                'status' => 'active',
            ],
            [
                'label' => 'Pinecone Vector Core (Eng Hedra)',
                'account_email' => 'eng.hedra.adel@gmail.com',
                'provider' => 'pinecone',
                'api_key' => 'pcsk_5vwo9j_AnUBnQQd4XAmt5suyvxkEaDN9SF2xwqCja5gnTmyGcb8xzX8BDghKy6AAg5YwX2',
                'scope' => 'all',
                'priority' => 1,
                'status' => 'active',
            ],
        ];

        foreach ($keys as $data) {
            AiApiKey::updateOrCreate(
                [
                    'provider' => $data['provider'],
                    'api_key' => $data['api_key'],
                ],
                $data
            );
        }

        // Set default dynamic AI models if not already set
        if (!Setting::get('ai_admin_model')) {
            Setting::set('ai_admin_model', 'gemini-3.8-flash', 'system', 'Dynamic AI Model for Admin Sovereign Agent');
        }
        if (!Setting::get('ai_visitor_model')) {
            Setting::set('ai_visitor_model', 'gemini-3.8-flash', 'system', 'Dynamic AI Model for Visitor/Customer AI Chat');
        }
    }
}
