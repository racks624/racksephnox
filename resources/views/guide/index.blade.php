@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-6">
            <h1 class="text-3xl font-bold text-gold mb-6">📘 User Guide</h1>

            <div class="space-y-8">
                <section>
                    <h2 class="text-xl font-semibold text-gold mb-2">💰 How to Deposit</h2>
                    <ol class="list-decimal list-inside space-y-1 text-ivory/80">
                        <li>Go to <strong>Deposit</strong> page.</li>
                        <li>Copy the displayed Pochi La Biashara number.</li>
                        <li>Send the exact amount via M-Pesa to that number.</li>
                        <li>Enter the M-Pesa transaction code and submit.</li>
                        <li>Wait for admin verification (within 48 hours).</li>
                        <li>Once verified, funds appear in your wallet.</li>
                    </ol>
                    <div class="mt-2 p-3 bg-gold/10 rounded-lg">
                        <p class="text-sm text-gold">🎁 Bonus: First deposit gets KES 40 extra. Every consecutive deposit gets KES 20 bonus.</p>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gold mb-2">💸 How to Withdraw</h2>
                    <ol class="list-decimal list-inside space-y-1 text-ivory/80">
                        <li>Go to <strong>Wallet</strong> page.</li>
                        <li>Click <strong>Withdraw</strong>.</li>
                        <li>Enter amount (minimum KES 530).</li>
                        <li>Fee is deducted automatically based on amount.</li>
                        <li>Withdrawal requests are processed within 48 hours.</li>
                    </ol>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gold mb-2">🤖 How to Invest in a Machine</h2>
                    <p class="text-ivory/80">Example: Jane Warui deposits KES 300 and invests KES 260 in <strong>RX1 Machine – VIP 1</strong>.</p>
                    <p class="text-ivory/80 mt-2">Daily profit = (3/5 × 260) = KES 156 over 14 days.<br>Total after 14 days = 260 + 156 = <strong>KES 416</strong>.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-gold mb-2">📈 Bitcoin Trading Guide</h2>
                    <p class="text-ivory/80">Minimum trade: KES 350 (converted to BTC).</p>
                    <ul class="list-disc list-inside mt-2 space-y-1 text-ivory/80">
                        <li><strong>Buy low, sell high</strong> – monitor the price chart.</li>
                        <li>Use <strong>limit orders</strong> to set your desired price.</li>
                        <li><strong>Market orders</strong> execute immediately.</li>
                        <li><strong>Trading bonus:</strong> Complete 8 trades within 24 hours and receive 8% of your trading balance as a bonus!</li>
                    </ul>
                </section>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('dashboard') }}" class="btn-golden">✨ Return to Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
