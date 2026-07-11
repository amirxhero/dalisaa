@props([
    'class' => 'h-10 w-auto',
    'variant' => 'full',
])

@if ($variant === 'icon')
<img
    src="{{ asset('static/media/DALISAA-ICON.png') }}"
    alt="{{ config('app.name') }}"
    {{ $attributes->merge(['class' => 'block shrink-0 object-contain ' . $class]) }}
>
@else
<img
    src="{{ asset('static/media/DALISAA-LOGO.png') }}"
    alt="{{ config('app.name') }}"
    {{ $attributes->merge(['class' => $class]) }}
>
@endif
