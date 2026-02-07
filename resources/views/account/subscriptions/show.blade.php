@extends('layouts.account')

@section('title', 'Subscription')

@section('content')
<div class="mb-6">
    <a href="{{ route('account.subscriptions.index') }}" class="text-violet-600 text-sm font-medium hover:underline">&larr; Back to subscriptions</a>
</div>

<x-card class="p-6 mb-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">{{ $subscription['product_title'] ?? 'Subscription' }}</h1>
    <p class="text-slate-600 text-sm">{{ $subscription['variant_title'] ?? '' }} · Quantity: {{ $subscription['quantity'] ?? 1 }}</p>
    <p class="text-slate-600 text-sm mt-1">Next charge: {{ isset($subscription['next_charge_scheduled_at']) ? \Carbon\Carbon::parse($subscription['next_charge_scheduled_at'])->format('F j, Y') : '—' }}</p>
    <x-badge :variant="($subscription['status'] ?? '') === 'active' ? 'success' : 'default'" class="mt-2">{{ $subscription['status'] ?? '—' }}</x-badge>
</x-card>

@if(in_array($subscription['status'] ?? '', ['active', 'queued']))
<div class="space-y-4" x-data="subscriptionActions({{ $subscription['id'] }})">
    @if(!empty($enabledFeatures['enable_pause']))
        <x-card class="p-5">
            <h2 class="font-semibold text-slate-800 mb-3">Pause / Resume</h2>
            <button @click="pause()" x-show="!loading" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Pause subscription</button>
            <button @click="resume()" x-show="!loading" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 ml-2">Resume</button>
            <span x-show="loading" class="text-sm text-slate-500">Saving...</span>
        </x-card>
    @endif

    <x-card class="p-5">
        <h2 class="font-semibold text-slate-800 mb-3">Change next charge date</h2>
        <form @submit.prevent="updateNextChargeDate()" class="flex flex-wrap gap-2 items-end">
            <input type="date" x-model="nextChargeDate" :min="minDate" class="rounded-xl border border-slate-300 px-3 py-2 text-sm focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
            <button type="submit" class="rounded-xl bg-violet-600 text-white px-4 py-2 text-sm font-medium hover:bg-violet-700" x-bind:disabled="loading">Update</button>
        </form>
    </x-card>

    <x-card class="p-5">
        <h2 class="font-semibold text-slate-800 mb-3">Quantity</h2>
        <form @submit.prevent="updateQuantity()" class="flex flex-wrap gap-2 items-end">
            <input type="number" x-model.number="quantity" min="1" class="rounded-xl border border-slate-300 px-3 py-2 text-sm w-24 focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20">
            <button type="submit" class="rounded-xl bg-violet-600 text-white px-4 py-2 text-sm font-medium hover:bg-violet-700" x-bind:disabled="loading">Update</button>
        </form>
    </x-card>

    @if(!empty($enabledFeatures['enable_cancel']))
        <x-card class="p-5">
            <h2 class="font-semibold text-slate-800 mb-3">Cancel subscription</h2>
            <button @click="cancel()" x-show="!loading" class="rounded-xl border border-red-300 text-red-700 px-4 py-2 text-sm font-medium hover:bg-red-50">Cancel subscription</button>
            <span x-show="loading" class="text-sm text-slate-500">Saving...</span>
        </x-card>
    @endif
</div>

<script>
function subscriptionActions(id) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const base = '{{ url("/api") }}';
    return {
        id,
        loading: false,
        nextChargeDate: '',
        quantity: {{ $subscription['quantity'] ?? 1 }},
        get minDate() {
            const d = new Date();
            d.setDate(d.getDate() + 1);
            return d.toISOString().slice(0, 10);
        },
        async api(path, method = 'POST', body = {}) {
            this.loading = true;
            try {
                const res = await fetch(base + path, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify(body),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || 'Request failed');
                if (document.body.__x && document.body.__x.$data && document.body.__x.$data.toast !== undefined)
                    document.body.__x.$data.toast = 'Saved.';
                setTimeout(() => location.reload(), 800);
            } catch (e) {
                alert(e.message);
            } finally {
                this.loading = false;
            }
        },
        async updateNextChargeDate() {
            if (!this.nextChargeDate) return;
            await this.api('/subscriptions/' + this.id + '/next-charge-date', 'POST', { date: this.nextChargeDate });
        },
        async updateQuantity() {
            await this.api('/subscriptions/' + this.id + '/quantity', 'POST', { quantity: this.quantity });
        },
        async pause() {
            await this.api('/subscriptions/' + this.id + '/pause');
        },
        async resume() {
            await this.api('/subscriptions/' + this.id + '/resume');
        },
        async cancel() {
            if (!confirm('Cancel this subscription?')) return;
            await this.api('/subscriptions/' + this.id + '/cancel', 'POST', { cancellation_reason: 'customer_request' });
        },
    };
}
</script>
@endif
@endsection
