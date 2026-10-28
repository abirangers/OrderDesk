<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Orders') · Orderdesk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-[13px] antialiased">
    <header class="border-b border-rule">
        <div class="mx-auto flex h-12 max-w-5xl items-center justify-between gap-4 px-4">
            <a href="{{ route('orders.index') }}" class="num text-[13px] font-semibold uppercase tracking-[0.14em]">Orderdesk</a>

            @auth
                <div class="flex items-center gap-4">
                    <span class="hidden text-ink-muted sm:inline">{{ auth()->user()->email }}</span>
                    <span class="num text-[11px] uppercase tracking-[0.1em] text-ink-muted">{{ auth()->user()->role }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-ink-muted underline underline-offset-2 hover:text-ink">Sign out</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-6">
        @if (session('status'))
            <div class="mb-4 border-l-2 border-oxide bg-surface px-3 py-2" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>