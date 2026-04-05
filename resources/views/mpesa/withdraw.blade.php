@extends('layouts.app')

@section('content')
<div x-data="withdrawalManager()" x-init="init()" class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold golden-title">Withdraw Funds</h1>
            <p class="text-gold-400 mt-2">Withdraw funds to your M-Pesa account</p>
        </div>

        <div class="card-golden p-6">
            <form @submit.prevent="submitWithdrawal()" class="space-y-6">
                <!-- Phone Number -->
                <div>
                    <label class="block text-gold-400 text-sm font-medium mb-2">M-Pesa Phone Number</label>
                    <input type="tel" 
                           x-model="phone" 
                           placeholder="254712345678" 
                           class="input-golden w-full"
                           :class="{'border-red-500': phoneError}"
                           @input="validatePhone()">
                    <p x-show="phoneError" x-text="phoneError" class="text-red-400 text-xs mt-1"></p>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-gold-400 text-sm font-medium mb-2">Amount (KES)</label>
                    <input type="number" 
                           x-model="amount" 
                           step="10" 
                           min="10" 
                           class="input-golden w-full"
                           :class="{'border-red-500': amountError}"
                           @input="validateAmount()">
                    <p x-show="amountError" x-text="amountError" class="text-red-400 text-xs mt-1"></p>
                    <div class="flex justify-between mt-2">
                        <div class="flex gap-2">
                            <button type="button" @click="amount = 1000" class="text-xs bg-gold/20 px-3 py-1 rounded-full text-gold-400 hover:bg-gold/30">KES 1,000</button>
                            <button type="button" @click="amount = 5000" class="text-xs bg-gold/20 px-3 py-1 rounded-full text-gold-400 hover:bg-gold/30">KES 5,000</button>
                            <button type="button" @click="amount = 10000" class="text-xs bg-gold/20 px-3 py-1 rounded-full text-gold-400 hover:bg-gold/30">KES 10,000</button>
                        </div>
                        <span class="text-xs text-ivory/50">Max: KES 150,000</span>
                    </div>
                </div>

                <!-- Balance Preview -->
                <div class="bg-gold/5 rounded-lg p-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-ivory/60">Your Balance</span>
                        <span class="text-gold" x-text="'KES ' + formatNumber(balance)"></span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-ivory/60">Withdrawal Amount</span>
                        <span class="text-gold" x-text="'KES ' + formatNumber(amount)"></span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-ivory/60">Withdrawal Fee</span>
                        <span class="text-ivory/60" x-text="'KES ' + formatNumber(fee)"></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-gold/30">
                        <span class="text-gold-400">Remaining Balance</span>
                        <span class="text-gold" x-text="'KES ' + formatNumber(Math.max(0, balance - amount - fee))"></span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="!isValid || isSubmitting"
                        class="btn-golden w-full disabled:opacity-50 disabled:cursor-not-allowed">
                    <i x-show="isSubmitting" class="fas fa-spinner fa-spin mr-2"></i>
                    <span x-text="isSubmitting ? 'Processing...' : 'Request Withdrawal'"></span>
                </button>
            </form>

            <!-- Info Box -->
            <div class="mt-6 p-4 bg-gold/5 rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-gold-400 mt-1"></i>
                    <div class="text-sm text-ivory/70">
                        <p>Withdrawals are processed within 24 hours. You will receive the funds to your M-Pesa account.</p>
                        <p class="mt-2">Withdrawal fee: {{ $feeRate ?? '1%' }} (minimum KES 10, maximum KES 100).</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showConfirmModal = false"></div>
            <div class="relative bg-cosmic-deep border border-gold/30 rounded-2xl p-6 max-w-md w-full mx-4">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-3"></i>
                    <h3 class="text-xl font-bold text-gold mb-2">Confirm Withdrawal</h3>
                    <p class="text-ivory/70 mb-4">Are you sure you want to withdraw KES {{ number_format($amount ?? 0, 2) }}?</p>
                    <div class="flex gap-3">
                        <button @click="showConfirmModal = false" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg">Cancel</button>
                        <button @click="confirmWithdrawal()" class="flex-1 btn-golden">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="relative bg-cosmic-deep border border-gold/30 rounded-2xl p-6 max-w-md w-full mx-4">
                <div class="text-center">
                    <i class="fas text-4xl mb-3" :class="modalType === 'success' ? 'fa-check-circle text-green-400' : 'fa-exclamation-circle text-red-400'"></i>
                    <h3 class="text-xl font-bold text-gold mb-2" x-text="modalTitle"></h3>
                    <p class="text-ivory/70 mb-4" x-text="modalMessage"></p>
                    <button @click="closeModal()" class="btn-golden w-full">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function withdrawalManager() {
    return {
        phone: '',
        amount: 0,
        balance: {{ $user->wallet->balance }},
        fee: 0,
        isSubmitting: false,
        phoneError: '',
        amountError: '',
        showConfirmModal: false,
        showModal: false,
        modalTitle: '',
        modalMessage: '',
        modalType: '',
        
        init() {
            this.validatePhone();
        },
        
        validatePhone() {
            const phoneRegex = /^254[0-9]{9}$/;
            if (!this.phone) {
                this.phoneError = '';
                return false;
            }
            if (!phoneRegex.test(this.phone)) {
                this.phoneError = 'Please enter a valid Kenyan phone number (e.g., 254712345678)';
                return false;
            }
            this.phoneError = '';
            return true;
        },
        
        validateAmount() {
            if (!this.amount || this.amount < 10) {
                this.amountError = 'Minimum withdrawal is KES 10';
                return false;
            }
            if (this.amount > 150000) {
                this.amountError = 'Maximum withdrawal is KES 150,000';
                return false;
            }
            this.fee = this.amount >= 100 ? Math.min(100, Math.ceil(this.amount * 0.01)) : 0;
            if (this.amount + this.fee > this.balance) {
                this.amountError = 'Insufficient balance (including fees)';
                return false;
            }
            this.amountError = '';
            return true;
        },
        
        get isValid() {
            return this.validatePhone() && this.validateAmount() && this.amount > 0;
        },
        
        formatNumber(num) {
            return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        
        submitWithdrawal() {
            if (!this.isValid) return;
            this.showConfirmModal = true;
        },
        
        async confirmWithdrawal() {
            this.showConfirmModal = false;
            this.isSubmitting = true;
            
            try {
                const response = await fetch('{{ route('mpesa.withdraw.initiate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        phone: this.phone,
                        amount: this.amount
                    })
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.error || 'Withdrawal request failed');
                }
                
                this.modalType = 'success';
                this.modalTitle = 'Withdrawal Requested!';
                this.modalMessage = 'Your withdrawal request has been submitted. Funds will be sent to your M-Pesa within 24 hours.';
                this.showModal = true;
                
            } catch (error) {
                this.modalType = 'error';
                this.modalTitle = 'Withdrawal Failed';
                this.modalMessage = error.message || 'Unable to process withdrawal. Please try again.';
                this.showModal = true;
            } finally {
                this.isSubmitting = false;
            }
        },
        
        closeModal() {
            this.showModal = false;
            if (this.modalType === 'success') {
                window.location.href = '{{ route('wallet') }}';
            }
        }
    }
}
</script>
@endsection
