<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Investment;
use App\Models\Transaction;
use App\Models\TradeOrder;
use App\Models\MachineInvestment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        // Summary stats
        $totalUsers = User::count();
        $verifiedUsers = User::where('is_verified', true)->count();
        $totalInvested = Investment::where('status', 'active')->sum('amount');
        $totalWithdrawn = Transaction::where('type', 'withdrawal')->sum('amount');
        $totalDeposited = Transaction::where('type', 'deposit')->sum('amount');
        $totalTradingVolume = TradeOrder::where('status', 'completed')->sum('amount_kes');
        $totalMachineInvested = MachineInvestment::where('status', 'active')->sum('amount');

        // Monthly data for charts
        $monthlyInvestments = Investment::selectRaw('strftime("%Y-%m", created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $monthlyUsers = User::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        $monthlyTradingVolume = TradeOrder::where('status', 'completed')
            ->selectRaw('strftime("%Y-%m", created_at) as month, SUM(amount_kes) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('admin.reports.index', compact(
            'totalUsers', 'verifiedUsers', 'totalInvested', 'totalWithdrawn',
            'totalDeposited', 'totalTradingVolume', 'totalMachineInvested',
            'monthlyInvestments', 'monthlyUsers', 'monthlyTradingVolume'
        ));
    }

    // CSV Exports
    public function exportUsers()
    {
        $users = User::with('wallet')->latest()->get();
        return $this->csvResponse($users, 'users', [
            'ID', 'Name', 'Email', 'Phone', 'Verified', 'Admin', 'Balance', 'Joined'
        ], function ($user) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $user->phone,
                $user->is_verified ? 'Yes' : 'No',
                $user->is_admin ? 'Yes' : 'No',
                $user->wallet->balance,
                $user->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function exportTransactions()
    {
        $transactions = Transaction::with('user')->latest()->get();
        return $this->csvResponse($transactions, 'transactions', [
            'ID', 'User', 'Type', 'Amount', 'Balance After', 'Description', 'Date'
        ], function ($tx) {
            return [
                $tx->id,
                $tx->user->name ?? 'N/A',
                $tx->type,
                $tx->amount,
                $tx->balance_after,
                $tx->description,
                $tx->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function exportInvestments()
    {
        $investments = Investment::with('user', 'plan')->latest()->get();
        return $this->csvResponse($investments, 'investments', [
            'ID', 'User', 'Plan', 'Amount', 'Daily Profit', 'Status', 'Start Date', 'End Date'
        ], function ($inv) {
            return [
                $inv->id,
                $inv->user->name ?? 'N/A',
                $inv->plan->name,
                $inv->amount,
                $inv->daily_profit,
                $inv->status,
                $inv->start_date->format('Y-m-d'),
                $inv->end_date->format('Y-m-d'),
            ];
        });
    }

    public function exportTrading()
    {
        $orders = TradeOrder::with('user')->where('status', 'completed')->latest()->get();
        return $this->csvResponse($orders, 'trading', [
            'ID', 'User', 'Side', 'Order Type', 'Amount (BTC)', 'Price', 'Total (KES)', 'Date'
        ], function ($order) {
            return [
                $order->id,
                $order->user->name ?? 'N/A',
                $order->side,
                $order->order_type,
                $order->filled_amount,
                $order->price_per_btc,
                $order->filled_kes,
                $order->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    // PDF Exports (requires barryvdh/laravel-dompdf)
    public function exportPdfReport()
    {
        $data = [
            'totalUsers' => User::count(),
            'totalInvested' => Investment::sum('amount'),
            'totalDeposited' => Transaction::where('type', 'deposit')->sum('amount'),
            'date' => now()->format('Y-m-d H:i'),
        ];
        $pdf = Pdf::loadView('pdf.report', $data);
        return $pdf->download('report_' . date('Y-m-d') . '.pdf');
    }

    private function csvResponse($data, $filename, $headers, $callback)
    {
        $response = new StreamedResponse(function () use ($data, $headers, $callback) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($data as $row) {
                fputcsv($handle, $callback($row));
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"',
        ]);
        return $response;
    }
}
