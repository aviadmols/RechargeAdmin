@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
<div class="bg-white rounded-xl shadow-md border border-slate-200 p-8">
    <h1 class="text-2xl font-semibold text-slate-800 mb-2">Sign in</h1>
    <p class="text-slate-600 text-sm mb-6">Enter your email to receive a one-time code.</p>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-800 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('login.request') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>
        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700">Send code</button>
    </form>
</div>
@endsection
