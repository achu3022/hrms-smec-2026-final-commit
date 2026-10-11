<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $allSettings = Setting::all();

        foreach ($allSettings as $setting) {
            $key = $setting->key;

            if ($setting->type === 'boolean') {
                $val = $request->has($key) ? '1' : '0';
                Setting::set($key, $val);
            } else {
                if ($request->has($key)) {
                    Setting::set($key, $request->input($key));
                }
            }
        }

        Setting::flushCache();

        return back()->with('success', 'System Settings updated successfully!');
    }
}
