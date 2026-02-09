@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
<div class="bg-white rounded-2xl shadow-lg border border-slate-200/80 p-8 max-w-md w-full">
    <div class="mb-6">
        <a href="{{ url('/') }}" class="inline-block"><img src="{{ config('mills.logo_url') }}" alt="{{ config('app.name') }}" class="h-9 w-auto" /></a>
    </div>
    <h1 class="text-2xl font-bold text-slate-800 mb-2">Sign in</h1>
    <p class="text-slate-600 text-sm mb-6">Welcome back. Sign in with your email.</p>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 text-red-800 text-sm border border-red-200">{{ $errors->first() }}</div>
    @endif

    <div class="mb-8">
        <p class="text-slate-600 text-sm mb-3">Sign in with email and password</p>
        <form action="{{ route('login.password') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="password_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" id="password_email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220] transition">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220] transition">
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" value="1"
                    class="rounded border-slate-300 text-[#002642] focus:ring-[#002642]">
                <label for="remember" class="ml-2 text-sm text-slate-600">Remember me</label>
            </div>
            <button type="submit" class="w-full rounded-xl text-white px-4 py-3 font-medium shadow-sm transition hover:opacity-90" style="background-color: #002642">Sign in with password</button>
        </form>
    </div>

    <div class="border-t border-slate-200 pt-6">
        <p class="text-slate-600 text-sm mb-3">Or receive a one-time code by email</p>
        <form action="{{ route('login.request') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-[#002642] focus:ring-2 focus:ring-[#00264220] transition">
            </div>
            <button type="submit" class="w-full rounded-xl border-2 px-4 py-2.5 font-medium transition" style="border-color: #00264240; color: #002642">Send code</button>
        </form>
    </div>
</div>
@endsection
