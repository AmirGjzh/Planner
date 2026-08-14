<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6">
        <h1 class="font-bold text-md mine-text-primary">My Plans</h1>
    </div>
    @island(name: 'plans-content', always: true)
    <div class="flex flex-col md:flex-row gap-2 sm:gap-4 mb-6">
        <div class="w-full flex items-center justify-between gap-2 sm:gap-4">
            <div class="w-full">
                <x-mine.input wire:model.live.debounce.200ms="search" placeholder="Search plans..."
                    leftIcon="magnifying-glass" />
            </div>
            <x-mine.modal.trigger id="add-plan-form">
                <x-mine.button type="button" class="mine-btn-outline-primary pl-3! pr-3! sm:pr-4!">
                    <div class="flex justify-center items-center gap-2">
                        <x-mine.icon name="plus" class="inline" variant="micro" />
                        <p class="text-sm font-medium hidden sm:inline"><span
                                class="hidden sm:inline md:hidden">New</span><span class="hidden md:inline">New Plan</span>
                        </p>
                    </div>
                </x-mine.button>
            </x-mine.modal.trigger>
            <x-mine.dropdown group="plan-filter">
                <x-mine.dropdown.trigger as="div">
                    <div
                        class="w-full mine-btn-outline-primary flex items-center h-11 px-4 sm:pl-3! rounded-xl cursor-pointer select-none">
                        <x-mine.icon name="arrow-long-up" class="inline -mr-1 -ml-2.5 sm:ml-0" variant="micro" />
                        <x-mine.icon name="arrow-long-down" class="inline -ml-1 -mr-2.5 sm:mr-0" variant="micro" />
                        <p class="text-sm font-medium hidden sm:inline sm:pl-2"><span
                                class="hidden sm:inline md:hidden">Sort</span><span class="hidden md:inline">Sort By</span>
                        </p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1!">
                    @foreach ([
                        'state' => 'State',
                        'deadline' => 'Deadline',
                        'load' => 'Load',
                        'latest' => 'Latest',
                        'name' => 'Name',
                    ] as $value => $label)
                        <x-mine.dropdown.item>
                            <div class="w-full py-2 px-4 flex items-center gap-2 hover:cursor-pointer"
                                @click="$wire.$island('plans-content').$set('sort', '{{ $value }}')">
                                <p
                                    class="text-sm font-medium  {{ $sort === $value ? 'mine-text-link' : 'mine-text-secondary' }}">
                                    {{ $label }}</p>
                                @if($sort === $value)
                                    <x-mine.icon variant="micro" name="check" class="size-4 mine-text-link" />
                                @endif
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
        <div class="w-full md:w-auto flex justify-between gap-2">
            <x-mine.dropdown group="plan-filter" class="w-full md:w-auto">
                <x-mine.dropdown.trigger as="div" class="w-full md:w-auto">
                    <div
                        class="w-full md:w-auto mine-btn-outline-primary flex items-center justify-center gap-2 h-11 px-3 sm:pr-4! rounded-xl cursor-pointer select-none">
                        <x-mine.icon name="funnel" class="" variant="micro" />
                        <p class="text-sm font-medium">
                            Filter Plan
                        </p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1!" placement="bottom-start">
                    @foreach ([
                        'all' => 'All',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'overdue' => 'Overdue',
                    ] as $value => $label)
                        <x-mine.dropdown.item>
                            <div class="w-full py-2 px-4 flex items-center gap-2 hover:cursor-pointer"
                                @click="$wire.$island('plans-content').$set('status_filter', '{{ $value }}')">
                                <p
                                    class="text-sm font-medium  {{ $status_filter === $value ? 'mine-text-link' : 'mine-text-secondary' }}">
                                    {{ $label }}</p>
                                @if($status_filter === $value)
                                    <x-mine.icon variant="micro" name="check" class="size-4 mine-text-link" />
                                @endif
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="plan-filter" class="md:hidden w-full">
                <x-mine.dropdown.trigger as="div" class=" w-full">
                    <div
                        class="w-full mine-btn-outline-primary flex items-center justify-center gap-2 h-11 px-3 sm:pr-4! rounded-xl cursor-pointer select-none">
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
        </div>
    </div>
    <div class="md:flex md:gap-6">
        <div class="relative flex-1">
            <div wire:loading.delay.short class="absolute inset-0 z-10">
                <div class="flex h-full w-full items-center justify-center">
                    <svg class="size-8 animate-spin mine-text-secondary" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" role="status" aria-label="Loading">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                </div>
            </div>
            <div wire:loading.delay.short.class="opacity-40" class="transition-opacity">
                <div class="grid grid-cols-1 gap-4">
                    @forelse ($this->plans as $plan)
                        @php
                            $pct = $plan->tasks_count > 0 ? (int) round($plan->tasks_done_count / $plan->tasks_count * 100) : 0;

                            $config = match (true) {
                                $plan->done => [
                                    'icon' => 'trophy',
                                    'header_class' => 'mine-badge-secondary',
                                    'title_class' => 'line-through',
                                    'status_label' => 'Completed',
                                    'percent_color' => 'mine-text-secondary-accent',
                                    'span_color' => 'mine-text-secondary-accent',
                                    'progress_variant' => 'gray',
                                    'progress_total' => $plan->tasks_count ?: 1,
                                    'progress_value' => $plan->tasks_count ? $plan->tasks_done_count : 1,
                                    'pct' => $plan->tasks_count > 0 ? $pct : 100,
                                    'check_badge' => 'mine-badge-secondary',
                                    'xmark_badge' => 'mine-badge-secondary',
                                    'days' => null,
                                    'action' => 'reopen',
                                ],
                                $plan->finish_date->isPast() => [
                                    'icon' => 'bell-alert',
                                    'header_class' => 'mine-badge-danger',
                                    'title_class' => '',
                                    'status_label' => 'Overdue',
                                    'percent_color' => 'mine-text-error',
                                    'span_color' => 'mine-text-error',
                                    'progress_variant' => 'danger',
                                    'progress_total' => '100',
                                    'progress_value' => $pct,
                                    'pct' => $pct,
                                    'check_badge' => 'mine-badge-secondary',
                                    'xmark_badge' => 'mine-badge-danger',
                                    'days' => [
                                        'value' => (int) -now()->diffInDays($plan->finish_date),
                                        'label' => 'days overdue',
                                        'width' => 'sm:min-w-40',
                                        'badge' => 'mine-badge-danger',
                                    ],
                                    'action' => 'complete',
                                    'add_class' => 'mine-btn-outline-danger',
                                    'complete_class' => 'mine-btn-danger',
                                ],
                                default => [
                                    'icon' => 'rocket-launch',
                                    'header_class' => 'mine-badge-primary',
                                    'title_class' => '',
                                    'status_label' => 'Active',
                                    'percent_color' => 'mine-text-link',
                                    'span_color' => 'mine-text-link',
                                    'progress_variant' => 'primary',
                                    'progress_total' => '100',
                                    'progress_value' => $pct,
                                    'pct' => $pct,
                                    'check_badge' => 'mine-badge-primary',
                                    'xmark_badge' => 'mine-badge-secondary',
                                    'days' => [
                                        'value' => max(0, (int) now()->diffInDays($plan->finish_date)),
                                        'label' => 'days left',
                                        'width' => 'sm:min-w-35',
                                        'badge' => 'mine-badge-primary',
                                    ],
                                    'action' => 'complete',
                                    'add_class' => 'mine-btn-outline-primary',
                                    'complete_class' => 'mine-btn-primary',
                                ],
                            };
                        @endphp

                        <div wire:key="plan-{{ $plan->id }}"
                            class="self-start mine-card-interactive flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8">
                            <div class="flex justify-between gap-4 mb-6 min-w-0">
                                <div class="flex min-w-0 flex-1">
                                    <div class="shrink-0 {{ $config['header_class'] }} rounded-xl size-16 flex justify-center items-center mr-4">
                                        <x-mine.icon :name="$config['icon']" class="size-8" />
                                    </div>
                                    <div class="flex flex-col justify-between gap-2 min-w-0">
                                        <div class="flex items-center gap-4 min-w-0">
                                            <h1 class="text-md font-bold mine-text-primary truncate min-w-0 {{ $config['title_class'] }}">{{ $plan->name }}</h1>
                                            <div class="{{ $config['header_class'] }} rounded-lg h-7 flex justify-center items-center px-2 shrink-0">
                                                <p class="text-[14px] font-medium">{{ $config['status_label'] }}</p>
                                            </div>
                                        </div>
                                        <p class="text-sm font-medium mine-text-secondary min-w-0 line-clamp-2 {{ $config['title_class'] }}">
                                            {{ $plan->description ?: 'No description' }}</p>
                                    </div>
                                </div>
                                <div class="flex justify-end items-start h-full shrink-0">
                                    <x-mine.dropdown group="plan-actions">
                                        <x-mine.dropdown.trigger>
                                            <div class="mine-btn-icon p-2 rounded-xl">
                                                <x-mine.icon name="ellipsis-horizontal" class="size-5" />
                                            </div>
                                        </x-mine.dropdown.trigger>
                                        <x-mine.dropdown.content>
                                            <x-mine.dropdown.item href="{{ route('tasks', ['plan_filter' => [$plan->id]]) }}">
                                                <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-link">
                                                    <x-mine.icon name="chevron-left" class="size-4" variant="micro" />
                                                    <p class="text-sm font-medium">View Tasks</p>
                                                </div>
                                            </x-mine.dropdown.item>
                                            <x-mine.dropdown.divider class="-mx-1" />
                                            <x-mine.dropdown.item>
                                                <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-link hover:cursor-pointer"
                                                    @click.stop="$wire.set('editing_id', {{ $plan->id }}, false);
                                                            $wire.set('edit_name', @js($plan->name), false);
                                                            $wire.set('edit_description', @js($plan->description), false);
                                                            $wire.set('edit_range', @js(['start' => $plan->start_date?->format('Y-m-d'), 'end' => $plan->finish_date?->format('Y-m-d')]), false);
                                                            $dispatch('open-modal', { id: 'edit-plan-form' })">
                                                    <x-mine.icon name="pencil" class="size-4" variant="solid" />
                                                    <p class="text-sm font-medium">Edit</p>
                                                </div>
                                            </x-mine.dropdown.item>
                                            <x-mine.dropdown.item destructive>
                                                <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-error hover:cursor-pointer"
                                                    @click.stop="$wire.set('deleting_id', {{ $plan->id }}, false);
                                                            $dispatch('open-modal', { id: 'delete-plan-confirmation' })">
                                                    <x-mine.icon name="trash" class="size-4" variant="solid" />
                                                    <p class="text-sm font-medium">Delete</p>
                                                </div>
                                            </x-mine.dropdown.item>
                                        </x-mine.dropdown.content>
                                    </x-mine.dropdown>
                                </div>
                            </div>
                            <div class="flex justify-between items-center gap-3">
                                <div class="flex items-center min-w-0">
                                    <div class="{{ $config['header_class'] }} rounded-lg size-8 flex justify-center items-center mr-3 shrink-0">
                                        <x-mine.icon name="calendar" variant="micro" />
                                    </div>
                                    <p class="text-sm font-medium mine-text-secondary">{{ $plan->start_date->format('Y-m-d') }}</p>
                                    <div class="mine-text-secondary flex justify-center items-center mx-2">
                                        <x-mine.icon name="arrow-long-right" variant="mini" />
                                    </div>
                                    <p class="text-sm font-medium mine-text-secondary">{{ $plan->finish_date->format('Y-m-d') }}</p>
                                </div>
                                @if($config['days'])
                                    <div class="flex justify-end items-center">
                                        <div class="{{ $config['days']['badge'] }} rounded-lg h-8 flex justify-center items-center pr-1.5 pl-2 sm:pr-2 sm:pl-1.5 {{ $config['days']['width'] }}">
                                            <p class="sm:hidden text-[14px] font-medium mr-2">
                                                {{ $config['days']['value'] }}</p>
                                            <x-mine.icon name="clock" variant="micro" class="sm:hidden " />
                                            <x-mine.icon name="clock" variant="micro" class="hidden sm:inline " />
                                            <p class="hidden sm:inline text-[14px] font-medium ml-2">
                                                {{ $config['days']['value'] }} <span class="hidden sm:inline">{{ $config['days']['label'] }}</span></p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="pt-4 pb-3 opacity-50">
                                <x-mine.separator />
                            </div>
                            <div class="flex justify-between items-center gap-6">
                                <div class="flex flex-col justify-between flex-2">
                                    <h2 class="text-sm font-medium mine-text-primary mb-2">Progress</h2>
                                    <div class="flex items-center justify-between mb-2">
                                        <h2 class="text-xl font-medium {{ $config['percent_color'] }}">
                                            {{ $config['pct'] }}%
                                        </h2>
                                        <p class="sm:hidden text-[14px] font-medium mine-text-secondary">
                                            <span class="{{ $config['span_color'] }} text-md font-bold">{{ $plan->tasks_done_count }}</span> of
                                            <span class="mine-text-primary text-md font-bold">{{ $plan->tasks_count }}</span>
                                            completed
                                        </p>
                                    </div>
                                    <x-mine.progress total="{{ $config['progress_total'] }}"
                                        progress="{{ $config['progress_value'] }}"
                                        variant="{{ $config['progress_variant'] ?? 'primary' }}"
                                        class="h-3" />
                                </div>
                                <div class="hidden sm:flex flex-col items-end flex-1 gap-2">
                                    <div class="{{ $config['check_badge'] }} rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                                        <x-mine.icon name="check" variant="micro" />
                                        <p class="text-[14px] font-medium ml-2">{{ $plan->tasks_done_count }} completed</p>
                                    </div>
                                    <div class="{{ $config['xmark_badge'] }} rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                                        <x-mine.icon name="x-mark" variant="micro" />
                                        <p class="text-[14px] font-medium ml-2">{{ $plan->tasks_count - $plan->tasks_done_count }}
                                            remaining</p>
                                    </div>
                                </div>
                            </div>
                            <div class="py-4 opacity-50">
                                <x-mine.separator />
                            </div>
                            <div class="flex gap-4 justify-between items-center">
                                @if($config['action'] === 'reopen')
                                    <x-mine.button type="button" class="mine-btn-outline-secondary"
                                        @click.stop="$wire.set('reopening_id', {{ $plan->id }}, false); $dispatch('open-modal', { id: 'reopen-plan-confirmation' })">
                                        <div class="flex justify-center items-center gap-1 text-sm font-medium">
                                            <x-mine.icon name="arrow-path" variant="micro" />
                                            <p>Reopen Plan</p>
                                        </div>
                                    </x-mine.button>
                                @else
                                    <a href="{{ route('tasks', ['plan_filter' => [$plan->id], 'add_plan' => $plan->id]) }}" wire:navigate.hover class="w-full">
                                        <x-mine.button type="button" class="{{ $config['add_class'] }}">
                                            <div class="flex justify-center items-center gap-1 text-sm font-md">
                                                <x-mine.icon name="plus" variant="micro" />
                                                <p>Add Task</p>
                                            </div>
                                        </x-mine.button>
                                    </a>
                                    <x-mine.button type="button" class="{{ $config['complete_class'] }}"
                                        @click.stop="$wire.set('completing_id', {{ $plan->id }}, false); $dispatch('open-modal', { id: 'complete-plan-confirmation' })">
                                        <div class="flex justify-center items-center gap-1 text-sm font-medium">
                                            <x-mine.icon name="check" variant="micro" />
                                            <p class="hidden sm:block">Mark as Completed</p>
                                            <p class="sm:hidden">Complete</p>
                                        </div>
                                    </x-mine.button>
                                @endif
                            </div>
                        </div>
                    @empty
                        @if($this->hasActiveFilters)
                            <div class="col-span-full text-center py-12">
                                <x-mine.icon name="magnifying-glass" class="size-12 mx-auto mb-3 mine-text-secondary" />
                                <p class="mine-text-secondary text-sm font-medium">No plans found.</p>
                            </div>
                        @else
                            <div class="col-span-full text-center py-12">
                                <x-mine.icon name="rocket-launch" class="size-12 mx-auto mb-3 mine-text-secondary" />
                                <p class="mine-text-secondary text-sm font-medium">No plans yet.</p>
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>
        </div>
        <div class="hidden md:flex justify-center w-80">
            <x-mine.calendar wire:model.live="range_filter" />
        </div>
    </div>
    <div class="mt-6 w-full">
        {{ $this->plans->links(data: ['scrollTo' => false]) }}
    </div>
    @endisland

    <x-mine.modal id="add-plan-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Add new plan</h2>
                <button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($add_success === 'created')
                <div class="mb-4">
                    <x-mine.alert variant="success" title="Plan created.">Your new plan has been
                        added.</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Plan already exists.">A plan with this name already
                        exists.</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="addPlan"
                @open-modal.window="if ($event.detail.id === 'add-plan-form') { $nextTick(() => $refs.addPlanInput?.focus()) }">
                <div>
                    <x-mine.input label="Plan Name" wire:model="add_name" placeholder="Enter plan name"
                        x-ref="addPlanInput" />
                </div>
                <div>
                    <x-mine.datepicker mode="range" position="bottom-end" wire:model="add_range" label="Date Range" />
                </div>
                <div>
                    <x-mine.textarea label="Plan Description" wire:model="add_description"
                        placeholder="Enter description" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="addPlan" class="mine-btn-primary">Add</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-plan-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Edit plan</h2>
                <button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($edit_success === 'updated')
                <div class="mb-4">
                    <x-mine.alert variant="success" title="Plan updated.">Your plan has been updated.</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Plan already exists.">A plan with this name already
                        exists.</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editPlan"
                @open-modal.window="if ($event.detail.id === 'edit-plan-form') { $nextTick(() => $refs.editPlanInput?.focus()) }">
                <div>
                    <x-mine.input label="Plan Name" wire:model="edit_name" placeholder="Enter plan name"
                        x-ref="editPlanInput" />
                </div>
                <div>
                    <x-mine.datepicker mode="range" position="bottom-end" wire:model="edit_range" label="Date Range" />
                </div>
                <div>
                    <x-mine.textarea label="Plan Description" wire:model="edit_description"
                        placeholder="Enter description" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="editPlan" class="mine-btn-primary">Save</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-plan-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Delete plan</h2>
                <button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($delete_error === 'has_tasks')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Cannot delete plan.">This plan has tasks. Reassign or
                        delete them first.</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Are you sure?">This action cannot be undone.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="deletePlan">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="deletePlan" class="mine-btn-danger">Delete</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="complete-plan-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Complete plan</h2>
                <button type="button" @click="close(); $wire.cancelComplete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($complete_error === 'has_undone_tasks')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Cannot complete plan.">This plan still has unfinished tasks.
                        Complete or remove them first.</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Are you sure?">This will mark the plan as completed.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="completePlan">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelComplete()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="completePlan" class="mine-btn-primary">Complete</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="reopen-plan-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Reopen plan</h2>
                <button type="button" @click="close(); $wire.cancelReopen()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            <div class="mb-4">
                <x-mine.alert variant="warning" title="Are you sure?">This plan will be moved back to active.</x-mine.alert>
            </div>

            <form class="flex flex-col gap-4" wire:submit="reopenPlan">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelReopen()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="reopenPlan" class="mine-btn-primary">Reopen</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>