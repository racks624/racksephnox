<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investment;
use App\Models\CryptoPrice;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache platform stats for 5 minutes to reduce database load
        $stats = Cache::remember('platform_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'total_invested' => Investment::sum('amount'),
                'active_investments' => Investment::where('status', 'active')->count(),
                'total_profit_paid' => Investment::where('status', 'completed')->sum('total_projected_profit'),
            ];
        });

        // Cache crypto prices for 5 minutes
        $cryptoPrices = Cache::remember('crypto_prices_home', 300, function () {
            return CryptoPrice::latest()->take(3)->get();
        });

        // Optional: fetch recent blog posts or news (if you have a News model)
        // $recentNews = News::latest()->take(3)->get();

        return view('home', compact('stats', 'cryptoPrices')); // , 'recentNews'
    }
}
