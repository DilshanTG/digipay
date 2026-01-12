<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::latest()->get();
        return view('admin.merchants.index', compact('merchants'));
    }

    public function create()
    {
        return view('admin.merchants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string',
            'return_url' => 'nullable|string',
            'cancel_url' => 'nullable|string',
            'notify_url' => 'nullable|string'
        ]);

        $apiKey = 'sk_live_' . bin2hex(random_bytes(16));

        Merchant::create([
            'name' => $request->name,
            'api_key' => $apiKey,
            'secret_key' => bin2hex(random_bytes(32)),
            'allowed_domains' => [$request->domain],
            'return_url' => $request->return_url,
            'cancel_url' => $request->cancel_url,
            'notify_url' => $request->notify_url,
            'is_active' => true
        ]);

        return redirect()->route('admin.merchants.index')->with('success', 'Merchant created! API Key: ' . $apiKey);
    }

    public function edit(Merchant $merchant)
    {
        return view('admin.merchants.edit', compact('merchant'));
    }

    public function update(Request $request, Merchant $merchant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domains' => 'required|string',
            'is_active' => 'nullable', 
            'return_url' => 'nullable|string',
            'cancel_url' => 'nullable|string',
            'notify_url' => 'nullable|string'
        ]);

        $domains = array_map('trim', explode(',', $request->domains));

        $merchant->update([
            'name' => $request->name,
            'allowed_domains' => $domains,
            'return_url' => $request->return_url,
            'cancel_url' => $request->cancel_url,
            'notify_url' => $request->notify_url,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.merchants.index')->with('success', 'Merchant updated!');
    }

    public function destroy(Merchant $merchant)
    {
        $merchant->delete();
        return redirect()->route('admin.merchants.index')->with('success', 'Merchant deleted!');
    }

    public function regenerateKey(Merchant $merchant)
    {
        $newKey = 'sk_live_' . bin2hex(random_bytes(16));
        
        $merchant->update([
            'api_key' => $newKey,
            'secret_key' => bin2hex(random_bytes(32))
        ]);

        // Clear cache
        \Cache::forget("merchant:{$merchant->api_key}");

        return redirect()->route('admin.merchants.index')->with('success', 'New API Key: ' . $newKey);
    }
}
