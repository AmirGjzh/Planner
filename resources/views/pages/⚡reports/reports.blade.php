<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6">
        <h1 class="font-bold text-md mine-text-primary">My Reports</h1>
    </div>
    <div class="flex flex-col">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <div class="flex flex-wrap items-center gap-2 w-full">
                @foreach (['this_week' => 'This week', 'last_week' => 'Last week', 'this_month' => 'This month', 'last_month' => 'Last month', 'custom' => 'Custom'] as $value => $label)
                    <x-mine.button
                        type="button"
                        wire:click="selectPreset('{{ $value }}')"
                        @class([
                            'w-auto! text-sm! font-medium h-10!',
                            'mine-btn-primary' => $preset === $value,
                            'mine-btn-ghost' => $preset !== $value,
                        ])>
                        {{ $label }}
                    </x-mine.button>
                @endforeach
                <div class="flex items-center justify-end gap-2 ml-auto">
                    @if ($preset === 'custom')
                        <x-mine.dropdown group="reports-range" class="">
                            <x-mine.dropdown.trigger as="div" class="">
                                <div
                                    class="w-full mine-btn-outline-primary flex items-center justify-center gap-2 h-10 px-3 sm:pr-4! rounded-xl cursor-pointer select-none">
                                    <x-mine.icon name="calendar" class="" variant="micro" />
                                    <p class="text-sm font-medium">
                                        Date Range
                                    </p>
                                </div>
                            </x-mine.dropdown.trigger>
                            <x-mine.dropdown.content class="mt-1! flex justify-center">
                                <div class="flex justify-center items-center w-80">
                                    <x-mine.calendar wire:model.live="range_filter" :card="false" />
                                </div>
                            </x-mine.dropdown.content>
                        </x-mine.dropdown>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex flex-col mb-6">
            <h2 class="font-bold text-sm mine-text-primary mb-4">Tasks Summary</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                <div class="flex items-center justify-between mine-card p-4">
                    <div class="flex flex-col justify-between gap-4">
                        <p class="text-sm font-medium mine-text-secondary">Total Tasks</p>
                        <p class="text-lg font-bold mine-text-primary">{{ $this->stats['total_tasks'] }}</p>
                    </div>
                    <div class="rounded-full size-12 flex items-center justify-center mine-badge-primary">
                        <x-mine.icon name="clipboard-document-list" class="size-6" variant="micro" />
                    </div>
                </div>
                <div class="flex items-center justify-between mine-card p-4">
                    <div class="flex flex-col justify-between gap-4">
                        <p class="text-sm font-medium mine-text-secondary">Completed Tasks</p>
                        <p class="text-lg font-bold mine-text-primary">{{ $this->stats['completed_tasks'] }}</p>
                    </div>
                    <div class="rounded-full size-12 flex items-center justify-center mine-badge-primary">
                        <x-mine.icon name="check-circle" class="size-6" variant="micro" />
                    </div>
                </div>
                <div class="flex items-center justify-between mine-card p-4">
                    <div class="flex flex-col justify-between gap-4">
                        <p class="text-sm font-medium mine-text-secondary">Completion Rate</p>
                        <p class="text-lg font-bold mine-text-primary">{{ $this->stats['completion_rate'] }}%</p>
                    </div>
                    <div class="rounded-full size-12 flex items-center justify-center mine-badge-primary">
                        <x-mine.icon name="chart-pie" class="size-6" variant="micro" />
                    </div>
                </div>
                <div class="flex items-center justify-between mine-card p-4">
                    <div class="flex flex-col justify-between gap-4">
                        <p class="text-sm font-medium mine-text-secondary">Estimated Time</p>
                        <p class="text-lg font-bold mine-text-primary">{{ $this->stats['estimated_time'] }}</p>
                    </div>
                    <div class="rounded-full size-12 flex items-center justify-center mine-badge-primary">
                        <x-mine.icon name="clock" class="size-6" variant="micro" />
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col mb-6">
            <h2 class="font-bold text-sm mine-text-primary mb-4">Plans Summary</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                <div class="flex items-center justify-between mine-card p-4">
                    <div class="flex flex-col justify-between gap-4">
                        <p class="text-sm font-medium mine-text-secondary">Total Plans</p>
                        <p class="text-lg font-bold mine-text-primary">{{ $this->stats['total_plans'] }}</p>
                    </div>
                    <div class="rounded-full size-12 flex items-center justify-center mine-badge-primary">
                        <x-mine.icon name="rocket-launch" class="size-6" variant="micro" />
                    </div>
                </div>
                <div class="flex items-center justify-between mine-card p-4">
                    <div class="flex flex-col justify-between gap-4">
                        <p class="text-sm font-medium mine-text-secondary">Completed Plans</p>
                        <p class="text-lg font-bold mine-text-primary">{{ $this->stats['completed_plans'] }}</p>
                    </div>
                    <div class="rounded-full size-12 flex items-center justify-center mine-badge-primary">
                        <x-mine.icon name="check-circle" class="size-6" variant="micro" />
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col">
            <h2 class="font-bold text-sm mine-text-primary mb-4">Workload Chart</h2>
            <div class="flex items-center justify-end gap-4 mb-4">
                <div class="flex items-center justify-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-(--mine-btn-primary-bg) mt-0.5"></span>
                    <p class="text-xs font-medium mine-text-secondary">Completed</p>
                </div>
                <div class="flex items-center justify-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-(--mine-btn-danger-bg) mt-0.5"></span>
                    <p class="text-xs font-medium mine-text-secondary">Remaining</p>
                </div>
            </div>
            <div class="mine-card p-4">
                <x-mine.bar-chart
                    :items="$this->chart"
                    label-key="label"
                    :series="[
                        ['key' => 'completed', 'class' => 'bg-(--mine-btn-primary-bg)', 'label' => 'Completed'],
                        ['key' => 'remaining', 'class' => 'bg-(--mine-btn-danger-bg)', 'label' => 'Remaining'],
                    ]"
                    :format="$this->formatMinutes(...)" />
            </div>
        </div>
    </div>
</div>