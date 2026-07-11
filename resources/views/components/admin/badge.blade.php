@props(['tone' => 'gray'])

@php
    $map = [
        'gray'    => 'bg-gray-100 text-gray-600',
        'indigo'  => 'bg-indigo-50 text-indigo-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'amber'   => 'bg-amber-50 text-amber-600',
        'rose'    => 'bg-rose-50 text-rose-600',
        'sky'     => 'bg-sky-50 text-sky-600',
    ];
    $cls = $map[$tone] ?? $map['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap $cls"]) }}>
    {{ $slot }}
</span>
