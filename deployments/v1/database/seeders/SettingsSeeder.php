<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'payhere_mode' => env('PAYHERE_MODE', 'sandbox'),
            'payhere_merchant_id_sandbox' => '1233267',
            'payhere_secret_sandbox_localhost' => 'MTY3MjQ3ODAwNDEzMzcyMzEwOTQzNTIzNDM0NTQ2NDA0MzgzNTY4Nw==',
            'payhere_secret_sandbox_digimartstore' => 'ODI0MDk0MTY3Njk2ODA3MzUyNDE4MDY0MTc2NTI0NDY1MjM1Mw==',
            'sandbox_domain_selection' => 'localhost',
            'payhere_merchant_id_live' => env('PAYHERE_MERCHANT_ID', '230488'),
            'payhere_secret_live' => env('PAYHERE_MERCHANT_SECRET', 'MTM2NjY3NjE0MDMzODM2MDczNTMwNDYzMzg2MDkxNjgxMDgzNDg3'),
            'payhere_app_id_sandbox' => '',
            'payhere_app_secret_sandbox' => '',
            'payhere_app_id_live' => '',
            'payhere_app_secret_live' => '',
            'fake_descriptions_under_5k' => "{name} - graphics design\n{mobile} grafic work\n{email} desgin payment\nsocial media post - {name}\n{mobile} insta post\nlogo desing {name}",
            'fake_descriptions_under_10k' => "{name} - email template\n{mobile} newsletter template\nprofessional sigature - {name}\n{email} email campaign\nsms marketing - {name}",
            'fake_descriptions_over_10k' => "{name} - meta ad campaign\n5 page website {name}\n{mobile} ai video creation\nai generated website - {email}\nCURRENT_MONTH work {name}",
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
