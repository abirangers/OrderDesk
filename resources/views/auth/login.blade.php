@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
    <div class="mx-auto mt-16 max-w-sm">
        <h1 class="text-[20px] font-semibold leading-tight">Sign in</h1>
        <p class="mt-1 mb-6 text-ink-muted">Use the account your administrator created for you.</p>

        <form method="POST" action="{{ route('login') }}" class="border border-rule bg-surface p-4">
            @csrf

            <label class="mb-4 block">
                <span class="mb-1 block text-ink-muted">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       autocomplete="username"
                       class="w-full border border-rule px-2 py-1.5 @error('email') border-oxide @enderror">
                @error('email')
                    <span class="mt-1 block text-oxide">{{ $message }}</span>
                @enderror
            </label>

            <label class="mb-4 block">
                <span class="mb-1 block text-ink-muted">Password</span>
                <input type="password" name="password" required autocomplete="current-password"
                       class="w-full border border-rule px-2 py-1.5">
            </label>

            <label class="mb-4 flex items-center gap-2">
                <input type="checkbox" name="remember" value="1" class="border border-rule">
                <span class="text-ink-muted">Keep me signed in</span>
            </label>

            <button type="submit" class="w-full bg-oxide px-3 py-2 font-medium text-white hover:bg-[#9c3c24]">
                Sign in
            </button>
        </form>
    </div>
@endsection