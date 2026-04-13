@extends('layouts.app')

@section('content')
<div x-data="onboardingWizard()" x-init="init()" class="py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card-golden p-8">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-r from-gold-400 to-gold-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-route text-3xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold golden-title">Welcome to Racksephnox</h1>
                <p class="text-gold-400 mt-2">Let’s set up your divine investment journey</p>
            </div>

            <!-- Progress Steps -->
            <div class="flex justify-between mb-8">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex-1 text-center">
                        <div class="w-10 h-10 rounded-full mx-auto flex items-center justify-center"
                            :class="currentStep > index ? 'bg-green-500 text-white' : (currentStep === index ? 'bg-gold text-black' : 'bg-gold/20 text-gold-400')">
                            <span x-text="index + 1"></span>
                        </div>
                        <p class="text-xs mt-2" x-text="step.name"></p>
                    </div>
                </template>
            </div>

            <!-- Step Content -->
            <div class="mt-6">
                <!-- Step 1: Deposit -->
                <div x-show="currentStep === 0">
                    <h3 class="text-xl font-bold text-gold mb-4">💰 First Deposit</h3>
                    <p class="text-ivory/70 mb-4">Fund your wallet to start investing. Minimum deposit: KES 10.</p>
                    <div class="bg-cosmic-deep/50 rounded-lg p-4 mb-4">
                        <div class="flex gap-3">
                            <input type="number" x-model="depositAmount" placeholder="Amount (KES)" class="input-golden flex-1">
                            <button @click="initiateDeposit" class="btn-golden">Deposit via M-Pesa</button>
                        </div>
                        <p x-show="depositError" x-text="depositError" class="text-red-400 text-sm mt-2"></p>
                    </div>
                </div>

                <!-- Step 2: KYC -->
                <div x-show="currentStep === 1">
                    <h3 class="text-xl font-bold text-gold mb-4">🪪 KYC Verification</h3>
                    <p class="text-ivory/70 mb-4">Upload your ID to verify your identity.</p>
                    <form id="kyc-form" action="{{ route('kyc.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm text-gold-400 mb-1">Document Type</label>
                                <select name="document_type" class="input-golden w-full">
                                    <option value="national_id">National ID</option>
                                    <option value="passport">Passport</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gold-400 mb-1">File (jpg, png, pdf)</label>
                                <input type="file" name="document" accept="image/*,application/pdf" class="input-golden w-full">
                            </div>
                        </div>
                        <button type="submit" class="btn-golden">Upload Document</button>
                    </form>
                </div>

                <!-- Step 3: Select Machine -->
                <div x-show="currentStep === 2">
                    <h3 class="text-xl font-bold text-gold mb-4">🤖 Choose Your First RX Machine</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                        <template x-for="machine in machines" :key="machine.id">
                            <div class="bg-cosmic-deep/50 rounded-lg p-4 border border-gold/30 cursor-pointer hover:border-gold"
                                 :class="{'border-gold bg-gold/10': selectedMachine === machine.id}"
                                 @click="selectedMachine = machine.id">
                                <h4 class="font-bold text-gold" x-text="machine.name"></h4>
                                <p class="text-sm">VIP 1: KES <span x-text="machine.vip1_amount.toLocaleString()"></span></p>
                                <p class="text-xs text-green-400">Daily profit: KES <span x-text="machine.daily_profit_vip1"></span></p>
                            </div>
                        </template>
                    </div>
                    <button @click="investInMachine" class="btn-golden mt-4 w-full" :disabled="!selectedMachine">Invest Now</button>
                </div>

                <!-- Step 4: Notifications -->
                <div x-show="currentStep === 3">
                    <h3 class="text-xl font-bold text-gold mb-4">🔔 Notification Preferences</h3>
                    <form id="preferences-form" action="{{ route('profile.notifications.update') }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <label class="flex items-center justify-between">
                                <span>Email Deposit Alerts</span>
                                <input type="checkbox" name="email_deposit" checked>
                            </label>
                            <label class="flex items-center justify-between">
                                <span>Email Investment Updates</span>
                                <input type="checkbox" name="email_investment" checked>
                            </label>
                            <label class="flex items-center justify-between">
                                <span>Daily Digest</span>
                                <input type="checkbox" name="daily_digest">
                            </label>
                        </div>
                        <button type="submit" class="btn-golden mt-4 w-full">Complete Setup</button>
                    </form>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-8">
                <button @click="prevStep" x-show="currentStep > 0" class="btn-outline-silver">← Previous</button>
                <button @click="nextStep" x-show="currentStep < steps.length - 1" class="btn-golden">Next →</button>
            </div>
        </div>
    </div>
</div>

<script>
function onboardingWizard() {
    return {
        steps: [
            { name: 'Deposit', completed: false },
            { name: 'KYC', completed: false },
            { name: 'Machine', completed: false },
            { name: 'Notify', completed: false }
        ],
        currentStep: 0,
        depositAmount: '',
        depositError: '',
        machines: [],
        selectedMachine: null,

        async init() {
            await this.fetchMachines();
            // Check progress from backend (optional)
            const response = await fetch('/api/user/onboarding-status');
            const data = await response.json();
            this.currentStep = data.step || 0;
        },

        async fetchMachines() {
            const res = await fetch('/api/v1/machines');
            const data = await res.json();
            this.machines = data.data;
        },

        async initiateDeposit() {
            if (!this.depositAmount || this.depositAmount < 10) {
                this.depositError = 'Minimum deposit is KES 10';
                return;
            }
            this.depositError = '';
            const res = await fetch('/api/v1/deposit/stk', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ amount: this.depositAmount, phone: '{{ auth()->user()->phone }}' })
            });
            if (res.ok) {
                alert('STK Push sent. Please complete payment on your phone.');
                // Simulate completion – in real app you'd wait for callback
                this.steps[0].completed = true;
                this.nextStep();
            } else {
                const err = await res.json();
                this.depositError = err.message || 'Deposit failed';
            }
        },

        async investInMachine() {
            const res = await fetch(`/api/v1/machines/${this.selectedMachine}/invest`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ vip_level: 1 })
            });
            if (res.ok) {
                alert('Investment successful!');
                this.steps[2].completed = true;
                this.nextStep();
            } else {
                alert('Investment failed');
            }
        },

        nextStep() {
            if (this.currentStep < this.steps.length - 1) {
                this.currentStep++;
            } else {
                // Mark onboarding as completed and redirect to dashboard
                fetch('/api/user/complete-onboarding', { method: 'POST' }).then(() => {
                    window.location.href = '/dashboard';
                });
            }
        },
        prevStep() { if (this.currentStep > 0) this.currentStep--; }
    }
}
</script>
@endsection
