@props([
    'tone' => 'neutral',
])

<span @class(['ut-badge', 'ut-badge-' . $tone])>{{ $slot }}</span>
