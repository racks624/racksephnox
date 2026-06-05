<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EnterpriseMachineController extends Controller
{
    public function index()
    {
        $machines = Machine::where('is_active', true)->get();
        
        $data = $machines->map(function ($machine) {
            $vipDetails = $machine->getVIPDetails();
            $stats = $machine->getEnterpriseStats();
            
            return [
                'id' => $machine->id,
                'code' => $machine->code,
                'name' => $machine->name,
                'description' => $machine->description,
                'risk_profile' => $machine->risk_profile,
                'duration_days' => $machine->duration_days,
                'icon' => $machine->icon,
                'color' => $machine->color,
                'vip_tiers' => array_values($vipDetails),
                'stats' => $stats,
                'background' => asset("images/machines/{$machine->code}.jpg"),
            ];
        });
        
        return response()->json(['success' => true, 'data' => $data]);
    }
    
    public function show($code)
    {
        $machine = Machine::where('code', $code)->where('is_active', true)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $machine->id,
                'code' => $machine->code,
                'name' => $machine->name,
                'description' => $machine->description,
                'risk_profile' => $machine->risk_profile,
                'duration_days' => $machine->duration_days,
                'growth_rate' => $machine->growth_rate,
                'vip_tiers' => array_values($machine->getVIPDetails()),
                'stats' => $machine->getEnterpriseStats(),
            ]
        ]);
    }
}
