<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Racksephnox Report</title>
    <style>
        body { font-family: sans-serif; }
        h1 { color: #D4AF37; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Racksephnox – Divine Analytics Report</h1>
    <p>Generated: {{ $date }}</p>
    <h2>Summary</h2>
    <ul>
        <li>Total Users: {{ number_format($totalUsers) }}</li>
        <li>Total Invested: KES {{ number_format($totalInvested, 2) }}</li>
        <li>Total Deposits: KES {{ number_format($totalDeposited, 2) }}</li>
    </ul>
    <p>Thank you for using Racksephnox.</p>
</body>
</html>
