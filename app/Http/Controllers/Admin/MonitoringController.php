<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MonitoringService;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    protected $monitor;

    public function __construct(MonitoringService $monitor)
    {
        $this->monitor = $monitor;
    }

    public function index()
    {
        return view('admin.monitoring');
    }

    public function health()
    {
        return response()->json($this->monitor->getSystemHealth());
    }

    public function revenue()
    {
        return response()->json($this->monitor->getDailyRevenueSummary());
    }
}
