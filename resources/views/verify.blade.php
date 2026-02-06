@extends('layouts.guest')

@section('title', 'Verify code')

@section('content')
<div class="bg-white rounded-xl shadow-md border border-slate-200 p-8">
    <h1 class="text-2xl font-semibold text-slate-800 mb-2">Check your email</h1>
    <p class="text-slate-600 text-sm mb-6">We sent a 6-digit code to <strong>{{ $email }}</strong>. Enter it below.</p>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('verify.submit') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <div>
            <label for="code" class="block text-sm font-medium text-slate-700 mb-1">Code</label>
            <input type="text" name="code" id="code" maxlength="6" pattern="[0-9]{6}" placeholder="000000" required autofocus
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 text-center text-lg tracking-widest focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700">Verify</button>
    </form>
    <p class="mt-4 text-sm text-slate-500">
        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Use a different email</a>
    </p>
</div>
@endsection
