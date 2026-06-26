<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $start_time = Setting::getValue('start_time');
        $end_time   = Setting::getValue('end_time');
        return view('admin.settings', compact('start_time', 'end_time'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'start_time' => ['required', 'date'],
            'end_time'   => ['required', 'date', 'after:start_time'],
        ]);

        Setting::setValue('start_time', $request->input('start_time'));
        Setting::setValue('end_time',   $request->input('end_time'));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings saved!');
    }
}