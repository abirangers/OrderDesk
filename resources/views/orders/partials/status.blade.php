@php
    $colour = match ($status) {
        \App\Enums\OrderStatus::Draft => 'text-status-draft',
        \App\Enums\OrderStatus::Confirmed => 'text-status-confirmed',
        \App\Enums\OrderStatus::Shipped => 'text-status-shipped',
        \App\Enums\OrderStatus::Done => 'text-status-done',
    };
@endphp

<span class="num text-[11px] uppercase tracking-[0.1em] {{ $colour }}">{{ $status->value }}</span>