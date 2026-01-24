<?php

namespace App\Services;

use App\Models\Setting;

class FakeDescriptionService
{
    public function generate($amount, $customerName, $customerEmail, $customerPhone, $realDescription = null): string
    {
        $templates = $this->getTemplatesByAmount($amount);
        $template = $templates[array_rand($templates)];

        $firstName = explode(' ', $customerName)[0];
        $currentMonth = strtolower(date('M'));

        $description = str_replace(
            ['{name}', '{email}', '{mobile}', '{service}', 'CURRENT_MONTH'],
            [$firstName, $customerEmail, $customerPhone, $realDescription ?? 'work', $currentMonth],
            $template
        );

        return substr($description, 0, 100);
    }

    private function getTemplatesByAmount($amount): array
    {
        $settingKey = 'fake_descriptions_over_10k';
        if ($amount < 5000) {
            $settingKey = 'fake_descriptions_under_5k';
        } elseif ($amount < 10000) {
            $settingKey = 'fake_descriptions_under_10k';
        }

        $customTemplates = Setting::get($settingKey);

        if (!empty($customTemplates)) {
            $templates = array_filter(array_map('trim', explode("\n", $customTemplates)));
            if (!empty($templates)) {
                return array_values($templates);
            }
        }

        if ($amount < 5000) {
            return $this->getUnder5kTemplates();
        } elseif ($amount < 10000) {
            return $this->getUnder10kTemplates();
        } else {
            return $this->getOver15kTemplates();
        }
    }

    private function getUnder5kTemplates(): array
    {
        return [
            '{name} - graphics design',
            '{mobile} grafic work',
            '{email} desgin payment',
            'social media post - {name}',
            '{mobile} insta post',
            'logo desing {name}'
        ];
    }

    private function getUnder10kTemplates(): array
    {
        return [
            '{name} - email template',
            '{mobile} newsletter template',
            'professional sigature - {name}',
            '{email} email campaign',
            'sms marketing - {name}'
        ];
    }

    private function getOver15kTemplates(): array
    {
        return [
            '{name} - meta ad campaign',
            '5 page website {name}',
            '{mobile} ai video creation',
            'ai generated website - {email}',
            'CURRENT_MONTH work {name}'
        ];
    }
}
