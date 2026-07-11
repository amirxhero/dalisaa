@props(['status'])

@php
    $styles = [
        'pending' => 'bg-amber-50 text-amber-600',
        'processing' => 'bg-sky-50 text-sky-600',
        'paid' => 'bg-emerald-50 text-emerald-600',
        'shipped' => 'bg-sky-50 text-sky-600',
        'delivered' => 'bg-emerald-50 text-emerald-600',
        'cancelled' => 'bg-ink-100 text-ink-500',
        'failed' => 'bg-red-50 text-red-600',
    ];

    $label = \App\Models\Order::STATUS_LABELS[$status] ?? $status;
    $style = $styles[$status] ?? 'bg-ink-100 text-ink-500';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold $style"]) }}>
    {{ $label }}
</span>
