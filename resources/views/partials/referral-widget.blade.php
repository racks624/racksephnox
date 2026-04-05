<div class="card-golden p-5 mt-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-gold">Referral Program</h3>
        <a href="{{ route('referrals') }}" class="text-sm text-gold-400 hover:text-gold">View all →</a>
    </div>
    <div class="flex justify-between items-center">
        <div>
            <p class="text-gold-400 text-sm">Total Referrals</p>
            <p class="text-2xl font-bold text-gold">{{ $referralCount }}</p>
        </div>
        <div>
            <p class="text-gold-400 text-sm">Bonus Earned</p>
            <p class="text-2xl font-bold text-gold">KES {{ number_format($totalBonus ?? 0, 2) }}</p>
        </div>
        <i class="fas fa-users text-3xl text-gold/50"></i>
    </div>
    <div class="mt-4">
        <button onclick="copyDashboardLink()" class="btn-golden w-full text-sm py-2">Share Referral Link</button>
    </div>
</div>
<script>
function copyDashboardLink() {
    const link = "{{ url('/refer/' . auth()->user()->referral_code) }}";
    navigator.clipboard.writeText(link);
    alert('Link copied!');
}
</script>
