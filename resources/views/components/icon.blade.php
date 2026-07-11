@props(['name', 'class' => 'w-5 h-5'])

@php
    $strokeIcons = [
        'search' => '<circle cx="11" cy="11" r="7" /><path d="m21 21-4.35-4.35" />',
        'heart' => '<path d="M12 20.5s-7.5-4.6-10-9.3C.4 7.7 2.2 4 6 4c2.1 0 3.7 1.1 4.8 2.6C11.9 5.1 13.5 4 15.6 4c3.8 0 5.6 3.7 4 7.2-2.5 4.7-10 9.3-10 9.3Z" />',
        'user' => '<circle cx="12" cy="8" r="3.5" /><path d="M4.5 20c1.4-3.6 4.4-5.5 7.5-5.5s6.1 1.9 7.5 5.5" />',
        'cart' => '<circle cx="9" cy="20.5" r="1" /><circle cx="17" cy="20.5" r="1" /><path d="M2.5 3.5h2l2.3 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 2-1.6l1.4-7.2H6" />',
        'home' => '<path d="M4 11.5 12 4l8 7.5" /><path d="M6 10v9.5a1 1 0 0 0 1 1h3v-6h4v6h3a1 1 0 0 0 1-1V10" />',
        'grid' => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.2" /><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.2" /><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.2" /><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.2" />',
        'fire' => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z" />',
        'chevron-down' => '<path d="m6 9 6 6 6-6" />',
        'chevron-left' => '<path d="m15 6-6 6 6 6" />',
        'chevron-right' => '<path d="m9 6 6 6-6 6" />',
        'close' => '<path d="m6 6 12 12M18 6 6 18" />',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16" />',
        'eye' => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" /><circle cx="12" cy="12" r="2.6" />',
        'shuffle' => '<path d="m17 3 4 4-4 4" /><path d="M3 7h6.5a4 4 0 0 1 3.3 1.8L15 12" /><path d="m17 21 4-4-4-4" /><path d="M3 17h6.5a4 4 0 0 0 3.3-1.8L14 12" />',
        'star' => '<path d="m12 3 2.6 5.8 6.3.6-4.8 4.2 1.4 6.2L12 16.8 6.5 19.8l1.4-6.2-4.8-4.2 6.3-.6Z" />',
        'check' => '<path d="m5 12.5 4.5 4.5L19 7" />',
        'box' => '<path d="M3.5 8 12 3.5 20.5 8 12 12.5 3.5 8Z" /><path d="M3.5 8v8.5L12 21m8.5-13v8.5L12 21m0-8.5V21" />',
        'mobile' => '<rect x="7" y="2.5" width="10" height="19" rx="2.2" /><path d="M11 18.5h2" />',
        'tablet' => '<rect x="4.5" y="3" width="15" height="18" rx="2" /><path d="M11 18h2" />',
        'watch' => '<rect x="7.5" y="7.5" width="9" height="9" rx="2.2" /><path d="M9 7.5V4.5h6v3M9 16.5v3h6v-3" />',
        'headphone' => '<path d="M4 13.5v-1a8 8 0 0 1 16 0v1" /><rect x="3" y="13" width="4" height="6" rx="1.5" /><rect x="17" y="13" width="4" height="6" rx="1.5" />',
        'headset' => '<path d="M4 14v-2a8 8 0 0 1 16 0v2" /><rect x="3" y="13.5" width="4" height="5.5" rx="1.4" /><rect x="17" y="13.5" width="4" height="5.5" rx="1.4" /><path d="M19 19v.8a2.7 2.7 0 0 1-2.7 2.7H13" />',
        'truck' => '<rect x="2.5" y="7" width="12" height="9" rx="1.2" /><path d="M14.5 10h3.4l3.1 3.4V16h-6.5" /><circle cx="7" cy="18.3" r="1.6" /><circle cx="17" cy="18.3" r="1.6" />',
        'cash' => '<rect x="2.5" y="6" width="19" height="12" rx="2" /><circle cx="12" cy="12" r="2.6" /><path d="M6 8v.01M18 16v.01" />',
        'return' => '<path d="M4 9a8 8 0 1 1-1.3 5.5" /><path d="M2 5v4.5h4.5" />',
        'shield' => '<path d="M12 3.5 19.5 6v6c0 5-3.2 7.7-7.5 8.7C7.7 19.7 4.5 17 4.5 12V6L12 3.5Z" /><path d="m9 12 2 2 4-4" />',
        'phone-call' => '<path d="M6.5 3.5 9 6c.3.5.2 1-.1 1.4L7.4 9c.9 2.2 2.9 4.2 5.1 5.1l1.6-1.5c.4-.3.9-.4 1.4-.1l2.5 2.5c.4.4.4 1.1-.1 1.5l-1.5 1.2c-.6.5-1.4.7-2.1.5-4-.9-8.2-5.1-9.1-9.1-.2-.7 0-1.5.5-2.1L6.5 3.5Z" />',
        'mail' => '<rect x="2.5" y="5" width="19" height="14" rx="2" /><path d="m3 6.5 9 6.5 9-6.5" />',
        'map-pin' => '<path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z" /><circle cx="12" cy="9.5" r="2.4" />',
        'play' => '<circle cx="12" cy="12" r="9.5" /><path d="M10 8.5v7l6-3.5-6-3.5Z" fill="currentColor" stroke="none" />',
        'plus' => '<path d="M12 5v14M5 12h14" />',
        'minus' => '<path d="M5 12h14" />',
        'clock' => '<circle cx="12" cy="12" r="9" /><path d="M12 7v5l3.2 2" />',
        'instagram' => '<rect x="3.5" y="3.5" width="17" height="17" rx="5" /><circle cx="12" cy="12" r="4" /><path d="M17 6.9v.01" />',
        'telegram' => '<path d="m20.5 4-18 7.3 5.4 1.8m12.6-9.1-2.3 15-8-5.9m10.3-9.1L9.9 15" />',
        'whatsapp' => '<path d="M6 18.5 3.7 20l1.5-4.3A8 8 0 1 1 12 20a7.9 7.9 0 0 1-3.9-1Z" /><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5" />',
    ];
@endphp

@if (isset($strokeIcons[$name]))
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['class' => $class]) }}>
        {!! $strokeIcons[$name] !!}
    </svg>
@endif
