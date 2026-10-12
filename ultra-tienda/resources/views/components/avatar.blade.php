@props([
    'label',
    'size' => 'size-9',
])

@php
    $initials = collect(explode(' ', trim($label)))
        ->filter()
        ->take(2)
        ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<span @class(['ut-avatar', $size]) aria-hidden="true">{{ $initials }}</span>
