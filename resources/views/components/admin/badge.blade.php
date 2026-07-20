@props(['tone' => 'gray'])

@php
    $map = [
        'gray'    => 'border-gray-200 bg-gray-50 text-gray-600',
        'indigo'  => 'border-indigo-200 bg-indigo-50 text-indigo-700',
        'emerald' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'amber'   => 'border-amber-200 bg-amber-50 text-amber-700',
        'rose'    => 'border-rose-200 bg-rose-50 text-rose-700',
        'sky'     => 'border-sky-200 bg-sky-50 text-sky-700',
    ];
    $cls = $map[$tone] ?? $map['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex min-h-7 items-center gap-1 rounded-full border px-2.5 py-1 text-[11px] font-semibold whitespace-nowrap $cls"]) }}>
    {{ $slot }}
</span>
