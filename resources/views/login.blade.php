@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
<div class="bg-white rounded-xl shadow-md border border-slate-200 p-8 max-w-md w-full">
    <h1 class="text-2xl font-semibold text-slate-800 mb-2">Sign in</h1>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ $errors->first() }}</div>
    @endif

    {{-- התחברות עם סיסמה (משתמשים שהאדמין הגדיר להם) --}}
    <div class="mb-8">
        <p class="text-slate-600 text-sm mb-3">Sign in with email and password</p>
        <form action="{{ route('login.password') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="password_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" id="password_email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" value="1"
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <label for="remember" class="ml-2 text-sm text-slate-600">Remember me</label>
            </div>
            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700">Sign in with password</button>
        </form>
    </div>

    <div class="border-t border-slate-200 pt-6">
        <p class="text-slate-600 text-sm mb-3">Or receive a one-time code by email</p>
        <form action="{{ route('login.request') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <button type="submit" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-slate-700 font-medium hover:bg-slate-50">Send code</button>
        </form>
    </div>
</div>
@endsection
