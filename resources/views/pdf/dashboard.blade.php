<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Summary</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Racksephnox Dashboard Summary</h1>
    <p>User: {{ $user->name }} ({{ $user->email }})</p>
    <p>Generated: {{ now()->format('Y-m-d H:i') }}</p>

    <h2>Wallet Balance: KES {{ number_format($user->wallet->balance, 2) }}</h2>

    <h3>Stats</h3>
    <ul>
        <li>Total Invested: KES {{ number_format($totalInvested, 2) }}</li>
        <li>Projected Profit: KES {{ number_format($totalProfit, 2) }}</li>
        <li>Active Investments: {{ $activeInvestments }}</li>
    </ul>

    <h3>Recent Transactions</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user->transactions()->latest()->take(10)->get() as $tx)
            <tr>
                <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ ucfirst($tx->type) }}</td>
                <td>{{ $tx->description }}</td>
                <td>{{ $tx->amount > 0 ? '+' : '' }}{{ number_format($tx->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
