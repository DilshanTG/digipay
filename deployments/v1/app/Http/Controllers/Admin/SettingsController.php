<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'payhere_mode' => Setting::get('payhere_mode', 'sandbox'),
            'payhere_merchant_id_sandbox' => Setting::get('payhere_merchant_id_sandbox', '1233267'),
            'payhere_secret_sandbox_localhost' => Setting::get('payhere_secret_sandbox_localhost', 'MTY3MjQ3ODAwNDEzMzcyMzEwOTQzNTIzNDM0NTQ2NDA0MzgzNTY4Nw=='),
            'payhere_secret_sandbox_digimartstore' => Setting::get('payhere_secret_sandbox_digimartstore', 'ODI0MDk0MTY3Njk2ODA3MzUyNDE4MDY0MTc2NTI0NDY1MjM1Mw=='),
            'sandbox_domain_selection' => Setting::get('sandbox_domain_selection', 'localhost'),
            'payhere_merchant_id_live' => Setting::get('payhere_merchant_id_live', env('PAYHERE_MERCHANT_ID', '')),
            'payhere_secret_live' => Setting::get('payhere_secret_live', env('PAYHERE_MERCHANT_SECRET', '')),
            'payhere_app_id_sandbox' => Setting::get('payhere_app_id_sandbox', ''),
            'payhere_app_secret_sandbox' => Setting::get('payhere_app_secret_sandbox', ''),
            'payhere_app_id_live' => Setting::get('payhere_app_id_live', ''),
            'payhere_app_secret_live' => Setting::get('payhere_app_secret_live', ''),
            'fake_descriptions_under_5k' => Setting::get('fake_descriptions_under_5k', ''),
            'fake_descriptions_under_10k' => Setting::get('fake_descriptions_under_10k', ''),
            'fake_descriptions_over_10k' => Setting::get('fake_descriptions_over_10k', ''),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'payhere_mode' => 'required|in:sandbox,live',
            'payhere_merchant_id_sandbox' => 'required',
            'payhere_secret_sandbox_localhost' => 'required',
            'payhere_secret_sandbox_digimartstore' => 'required',
            'sandbox_domain_selection' => 'required|in:localhost,digimartstore',
            'payhere_merchant_id_live' => 'required',
            'payhere_secret_live' => 'required',
            'payhere_app_id_sandbox' => 'nullable',
            'payhere_app_secret_sandbox' => 'nullable',
            'payhere_app_id_live' => 'nullable',
            'payhere_app_secret_live' => 'nullable',
            'fake_descriptions_under_5k' => 'nullable',
            'fake_descriptions_under_10k' => 'nullable',
            'fake_descriptions_over_10k' => 'nullable',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
