@props([
    'title',
    'description' => null,
    'breadcrumb' => [],
])

<div class="ut-page-header">
    <div>
        @if (!empty($breadcrumb))
            <nav aria-label="Ruta de navegación">
                <ol class="ut-breadcrumb">
                    @foreach ($breadcrumb as $index => $crumb)
                        <li class="flex items-center gap-1.5">
                            @if ($index > 0)
                                <span aria-hidden="true">/</span>
                            @endif
                            <span @class(['ut-breadcrumb-current' => $loop->last])>{{ $crumb }}</span>
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif

        <h1 class="ut-page-title">{{ $title }}</h1>

        @if ($description)
            <p class="ut-page-description">{{ $description }}</p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endif
</div>
