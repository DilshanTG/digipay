<?php

namespace App\Services;

class FakeDescriptionService
{
    public function generate($amount, $customerName, $customerEmail, $customerPhone, $realDescription = null)
    {
        $templates = $this->getTemplatesByAmount($amount);
        $template = $templates[array_rand($templates)];
        
        // Extract first name from full name
        $firstName = explode(' ', $customerName)[0];
        
        // Get current month
        $currentMonth = strtolower(date('M'));
        
        // Replace placeholders
        $description = str_replace(
            ['{name}', '{email}', '{mobile}', '{service}', 'CURRENT_MONTH'],
            [$firstName, $customerEmail, $customerPhone, $realDescription ?? 'work', $currentMonth],
            $template
        );
        
        return substr($description, 0, 100); // PayHere item limit
    }
    
    private function getTemplatesByAmount($amount)
    {
        $settingKey = 'fake_descriptions_over_10k';
        if ($amount < 5000) {
            $settingKey = 'fake_descriptions_under_5k';
        } elseif ($amount < 10000) {
            $settingKey = 'fake_descriptions_under_10k';
        }

        $customTemplates = \App\Models\Setting::get($settingKey);
        
        if (!empty($customTemplates)) {
            // Split by new line and filter empty lines
            $templates = array_filter(array_map('trim', explode("\n", $customTemplates)));
            if (!empty($templates)) {
                return array_values($templates);
            }
        }

        // Fallback to hardcoded defaults
        if ($amount < 5000) {
            return $this->getUnder5kTemplates();
        } elseif ($amount < 10000) {
            return $this->getUnder10kTemplates();
        } else {
            return $this->getOver15kTemplates();
        }
    }
    
    private function getUnder5kTemplates()
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
    
    private function getUnder10kTemplates()
    {
        return [
            '{name} - email template',
            '{mobile} newsletter template',
            'professional sigature - {name}',
            '{email} email campaign',
            'sms marketing - {name}'
        ];
    }
    
    private function getOver15kTemplates()
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
