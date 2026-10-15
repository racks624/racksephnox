<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DepositRequest;
use App\Models\MpesaStatement;
use App\Services\Deposit\DepositVerificationService;
use App\Services\Deposit\MpesaStatementParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositVerificationController extends Controller
{
    public function __construct(
        protected DepositVerificationService $service,
        protected MpesaStatementParser $parser
    ) {}

    /**
     * Admin dashboard — list all pending deposit verifications.
     */
    public function index(Request $request)
    {
        $query = DepositRequest::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        } else {
            $query->awaitingVerify();
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('transaction_code', 'like', "%{$s}%")
                  ->orWhere('reference', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $deposits = $query->paginate(25)->withQueryString();
        $stats    = $this->service->adminStats();

        $recentStatements = MpesaStatement::latest()->take(5)->get();

        return view('admin.deposits.index', compact('deposits', 'stats', 'recentStatements'));
    }

    /**
     * Approve a deposit and credit the user's wallet.
     */
    public function verify(Request $request, DepositRequest $deposit)
    {
        $data = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $result = $this->service->verify($deposit, Auth::user(), $data['notes'] ?? null);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Reject a deposit with a reason.
     */
    public function reject(Request $request, DepositRequest $deposit)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $result = $this->service->reject($deposit, Auth::user(), $data['reason']);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Upload an M-Pesa statement CSV for bulk matching.
     */
    public function uploadStatement(Request $request)
    {
        $data = $request->validate([
            'statement' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $statement = $this->parser->parse($data['statement'], Auth::id());
            $matches   = $this->parser->autoMatchAll($statement, $this->service);

            return back()->with('success', "Statement parsed. {$matches} deposits auto-matched.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Parse failed: ' . $e->getMessage());
        }
    }
}
