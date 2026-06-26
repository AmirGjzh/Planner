@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => ''],
        ['label' => 'Categories', 'route' => 'dashboard', 'icon' => ''],
        ['label' => 'Plans', 'route' => 'dashboard', 'icon' => ''],
        ['label' => 'Tasks', 'route' => 'dashboard', 'icon' => ''],
    ];
@endphp

<div>
    @if ($variant === 'navbar')
        @foreach ($links as $link)
            <x-ui.link variant="soft" class="text-slate-500! hover:text-slate-600! font-medium text-sm mx-4"
                href="{{ route($link['route']) }}" wire:navigate>
                {{ $link['label'] }}
            </x-ui.link>
        @endforeach
    @else
        <div class="flex flex-col">
            @foreach ($links as $link)
                <x-ui.link variant="soft" href="{{route($link['route'])}}" wire:navigate
                    class="text-slate-500! hover:text-slate-600! font-medium text-lg my-2 mx-2">
                    {{ $link['label'] }}
                </x-ui.link>
            @endforeach
        </div>
    @endif
</div>
