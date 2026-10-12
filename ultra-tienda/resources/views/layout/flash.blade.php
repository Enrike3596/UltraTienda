@if (session('success'))
    <div class="ut-alert ut-alert-success" role="status">
        <x-icon name="circle-check" size="size-5" class="mt-0.5 shrink-0" />
        <p class="font-medium">{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div class="ut-alert ut-alert-error" role="alert">
        <x-icon name="triangle-alert" size="size-5" class="mt-0.5 shrink-0" />
        <p class="font-medium">{{ session('error') }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="ut-alert ut-alert-error" role="alert">
        <x-icon name="triangle-alert" size="size-5" class="mt-0.5 shrink-0" />
        <div>
            <p class="font-semibold">Revisa los siguientes campos:</p>
            <ul class="mt-1 list-disc pl-5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
