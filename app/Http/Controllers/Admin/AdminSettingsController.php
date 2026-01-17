<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('category');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            
            if ($setting) {
                // Handle checkbox values (on/off)
                if ($setting->type === 'boolean') {
                    $value = $request->has($key) ? 'true' : 'false';
                }
                
                $setting->update(['value' => $value]);
            }
        }

        return back()->with('success', 'Settings updated successfully!');
    }

    public function general()
    {
        $settings = SystemSetting::whereIn('key', [
            'company_name',
            'company_address',
            'company_phone',
            'company_email',
            'timezone',
            'currency',
            'date_format',
            'time_format'
        ])->get();
        
        return view('admin.settings.general', compact('settings'));
    }

    public function notifications()
    {
        $settings = SystemSetting::whereIn('key', [
            'enable_email_notifications',
            'enable_sms_notifications',
            'enable_push_notifications',
            'email_from_address',
            'email_from_name',
            'sms_provider',
            'push_notification_key'
        ])->get();
        
        return view('admin.settings.notifications', compact('settings'));
    }

    public function courier()
    {
        $settings = SystemSetting::whereIn('key', [
            'location_update_interval',
            'auto_cancel_timeout',
            'max_stops_per_request',
            'enable_realtime_tracking',
            'default_pickup_timeout',
            'courier_radius_limit'
        ])->get();
        
        return view('admin.settings.courier', compact('settings'));
    }
}