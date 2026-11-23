<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Setting::class);
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return Inertia::render('Settings/Index', ['settings' => $settings]);
    }

    public function __construct()
    {
        $this->middleware('permission:edit');
    }

    public function edit($key)
    {
        $this->authorize('update', Setting::class);
        $setting = Setting::where('key', $key)->firstOrFail();
        return Inertia::render('Settings/Edit', ['setting' => $setting]);
    }

    public function update(Request $request, $key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        $value = $request->input('value');
        $setting->value = $value;
        $setting->save();
        return redirect()->route('admin.settings.index')->with('success', 'Setting updated');
    }
}
