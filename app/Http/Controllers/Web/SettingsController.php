<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $settings = $this->getSettings();
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $tab = $request->input('tab', 'general');
        
        $validated = match($tab) {
            'general' => $this->validateGeneral($request),
            'email' => $this->validateEmail($request),
            'sms' => $this->validateSMS($request),
            'localization' => $this->validateLocalization($request),
            default => [],
        };

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('settings', 'public');
            $validated['company_logo'] = Storage::url($path);
        }

        // Save settings
        foreach ($validated as $key => $value) {
            if ($key === 'company_logo' && !$request->hasFile('company_logo')) {
                continue;
            }
            
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string']
            );
        }

        // Clear settings cache
        Cache::forget('settings');

        return redirect()
            ->route('settings.index', ['tab' => $tab])
            ->with('success', 'Settings updated successfully');
    }

    public function testEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            Mail::raw('This is a test email from your e-commerce platform.', function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Test Email');
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function testSMS(Request $request)
    {
        $request->validate(['phone' => 'required']);

        // Implement SMS sending logic here
        return response()->json(['success' => true]);
    }

    private function getSettings()
    {
        return Cache::remember('settings', 3600, function() {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    private function validateGeneral(Request $request)
    {
        return $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'tax_id' => 'nullable|string|max:100',
            'company_logo' => 'nullable|image|max:2048',
            'website_url' => 'nullable|url',
            'support_email' => 'nullable|email',
            'support_phone' => 'nullable|string|max:50',
        ]);
    }

    private function validateEmail(Request $request)
    {
        return $request->validate([
            'mail_driver' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_encryption' => 'nullable|string',
            'mail_username' => 'required|string',
            'mail_password' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'mail_reply_to' => 'nullable|email',
        ]);
    }

    private function validateSMS(Request $request)
    {
        return $request->validate([
            'sms_provider' => 'required|string',
            'sms_enabled' => 'nullable|boolean',
            'twilio_account_sid' => 'nullable|string',
            'twilio_auth_token' => 'nullable|string',
            'twilio_phone_number' => 'nullable|string',
            'nexmo_api_key' => 'nullable|string',
            'nexmo_api_secret' => 'nullable|string',
            'nexmo_sender_id' => 'nullable|string',
            'sms_order_confirmation' => 'nullable|boolean',
            'sms_order_shipped' => 'nullable|boolean',
            'sms_order_delivered' => 'nullable|boolean',
            'sms_low_stock' => 'nullable|boolean',
        ]);
    }

    private function validateLocalization(Request $request)
    {
        return $request->validate([
            'currency' => 'required|string',
            'currency_format' => 'nullable|string',
            'locale' => 'required|string',
            'timezone' => 'required|string',
            'date_format' => 'nullable|string',
            'time_format' => 'nullable|string',
            'weight_unit' => 'nullable|string',
            'dimension_unit' => 'nullable|string',
        ]);
    }
}
