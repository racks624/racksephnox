<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => config('app.name'),
            'site_url' => config('app.url'),
            'maintenance_mode' => app()->isDownForMaintenance(),
            'referral_bonus_rate' => config('referral.bonus_rate', 5),
            'min_deposit' => env('MIN_DEPOSIT', 10),
            'min_withdrawal' => env('MIN_WITHDRAWAL', 530),
            'trading_min_amount' => env('TRADING_MIN_AMOUNT', 350),
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'referral_bonus_rate' => 'required|numeric|min:0|max:100',
            'min_deposit' => 'required|numeric|min:1',
            'min_withdrawal' => 'required|numeric|min:10',
            'trading_min_amount' => 'required|numeric|min:10',
        ]);

        // Update .env file (simple approach – in production use a package)
        $this->updateEnv('APP_NAME', $request->site_name);
        $this->updateEnv('REFERRAL_BONUS_RATE', $request->referral_bonus_rate);
        $this->updateEnv('MIN_DEPOSIT', $request->min_deposit);
        $this->updateEnv('MIN_WITHDRAWAL', $request->min_withdrawal);
        $this->updateEnv('TRADING_MIN_AMOUNT', $request->trading_min_amount);

        Cache::flush();
        Artisan::call('config:clear');

        return back()->with('success', 'Settings updated successfully.');
    }

    public function toggleMaintenance()
    {
        if (app()->isDownForMaintenance()) {
            Artisan::call('up');
            $message = 'Application is now live.';
        } else {
            Artisan::call('down --retry=60');
            $message = 'Application is now in maintenance mode.';
        }
        return back()->with('success', $message);
    }

    private function updateEnv($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, preg_replace(
                "/^{$key}=.*/m",
                "{$key}=\"{$value}\"",
                file_get_contents($path)
            ));
        }
    }
}
