<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default System Merchant
        \App\Models\Merchant::create([
            'name' => 'DigiMart System',
            'api_key' => 'sk_live_' . bin2hex(random_bytes(16)),
            'secret_key' => bin2hex(random_bytes(32)),
            'allowed_domains' => ['*'],
            'is_active' => true
        ]);
        // Seed Settings
        $this->call(SettingsSeeder::class);
    }
}
