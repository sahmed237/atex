<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->get('group', 'general');
        $settings = Setting::where('group', $group)->get();

        return view('admin.settings.index', compact('settings', 'group'));
    }

    public function update(Request $request)
    {
        $group = $request->get('group');
        $inputs = $request->except('_token', '_method', 'group');

        foreach ($inputs as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                if ($setting->type === 'boolean') {
                    $value = ($value === 'on' || $value === '1' || $value === true) ? '1' : '0';
                }
                $setting->update(['value' => $value]);
            }
        }

        // Handle File Uploads (Logo, etc)
        if ($request->hasFile('platform_logo')) {
            $path = $request->file('platform_logo')->store('logos', 'public');
            Setting::set('platform_logo', asset('storage/' . $path), 'general', 'image');
        }

        // Handle missing boolean values (unchecked checkboxes) for submitted form fields
        if ($group) {
            $submittedKeys = array_keys($inputs);
            $booleanSettings = Setting::where('group', $group)->where('type', 'boolean')->get();

            if (!empty($submittedKeys)) {
                $prefixes = array_unique(array_map(fn($k) => explode('_', $k)[0], $submittedKeys));
                $booleanSettings = $booleanSettings->filter(function ($setting) use ($prefixes) {
                    $settingPrefix = explode('_', $setting->key)[0];
                    return in_array($settingPrefix, $prefixes);
                });
            }

            foreach ($booleanSettings as $setting) {
                if (!isset($inputs[$setting->key])) {
                    $setting->update(['value' => '0']);
                }
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function deleteLogo()
    {
        $setting = Setting::where('key', 'platform_logo')->first();
        if ($setting && $setting->value) {
            // Extract path from URL
            $path = str_replace(asset('storage/'), '', $setting->value);
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
            $setting->update(['value' => '']);
        }

        return response()->json(['success' => true]);
    }

    public function sendTestMail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'nullable|string|in:general,kyc'
        ]);
        
        try {
            Setting::configureMailer();

            $mailer = ($request->type === 'kyc') ? \Illuminate\Support\Facades\Mail::mailer('smtp_kyc') : \Illuminate\Support\Facades\Mail::mailer();
            $mailer->to($request->email)->send(new \App\Mail\TestMail($request->type ?? 'general'));
            
            return response()->json(['success' => true, 'message' => 'Test email sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send mail: ' . $e->getMessage()], 500);
        }
    }
}
