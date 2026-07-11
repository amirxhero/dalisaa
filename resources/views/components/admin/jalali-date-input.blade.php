@props([
    'name',
    'label',
    'value' => '',
    'id' => null,
    'minDate' => null,
])

@php
    $inputId = $id ?? $name;
@endphp

<div>
    <label for="{{ $inputId }}" class="admin-label">{{ $label }}</label>
    <input type="text"
           id="{{ $inputId }}"
           name="{{ $name }}"
           value="{{ $value }}"
           data-jdp
           autocomplete="off"
           placeholder="مثال: ۱۴۰۳/۰۷/۱۵"
           @if($minDate) data-jdp-min-date="{{ $minDate }}" @endif
           {{ $attributes->class(['admin-input']) }}>
</div>
