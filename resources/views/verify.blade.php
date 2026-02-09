@extends('layouts.guest')

@section('title', 'Verify code')

@section('content')
<div class="bg-white rounded-2xl shadow-lg border border-slate-200/80 p-8 max-w-md w-full">
    <div class="mb-6">
        <a href="{{ url('/') }}" class="inline-block"><img src="{{ config('mills.logo_url') }}" alt="{{ config('app.name') }}" class="h-9 w-auto" /></a>
    </div>
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Check your email</h1>
    <p class="text-slate-600 text-sm mb-6">We sent a 6-digit code to <strong>{{ $email }}</strong>. Enter it below.</p>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 text-red-800 text-sm border border-red-200">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('verify.submit') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <div>
            <label for="code" class="block text-sm font-medium text-slate-700 mb-1">Code</label>
            <input type="text" name="code" id="code" maxlength="6" pattern="[0-9]{6}" placeholder="000000" required autofocus
                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 text-center text-lg tracking-widest focus:border-[#002642] focus:ring-2 focus:ring-[#00264220]">
        </div>
        <button type="submit" class="w-full rounded-xl text-white px-4 py-3 font-medium shadow-sm transition hover:opacity-90" style="background-color: #002642">Verify</button>
    </form>
    <p class="mt-4 text-sm text-slate-500">
        <a href="{{ route('login') }}" class="font-medium hover:underline" style="color: #002642">Use a different email</a>
    </p>
</div>
@endsection
