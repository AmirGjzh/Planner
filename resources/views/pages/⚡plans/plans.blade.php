<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6">
        <h1 class="font-bold text-md mine-text-primary">My Plans</h1>
    </div>
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
                    <x-mine.dropdown.item>
                        <div class="w-full py-2 px-4 flex items-center gap-2 hover:cursor-pointer"
                            @click="$wire.set('sort', 'latest')">
                            <p
                                class="text-sm font-medium flex-1 {{ $sort === 'latest' ? 'mine-text-link' : 'mine-text-secondary' }}">
                                Latest</p>
                            @if($sort === 'latest')
                                <x-mine.icon variant="micro" name="check" class="size-4 mine-text-link" />
                            @endif
                        </div>
                    </x-mine.dropdown.item>
                    <x-mine.dropdown.item>
                        <div class="w-full py-2 px-4 flex items-center gap-2 hover:cursor-pointer"
                            @click="$wire.set('sort', 'name')">
                            <p
                                class="text-sm font-medium flex-1 {{ $sort === 'name' ? 'mine-text-link' : 'mine-text-secondary' }}">
                                Name</p>
                            @if($sort === 'name')
                                <x-mine.icon variant="micro" name="check" class="size-4 mine-text-link" />
                            @endif
                        </div>
                    </x-mine.dropdown.item>
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
                <x-mine.dropdown.content class="mt-1!">
                    @foreach ([
                        'all' => 'All',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'overdue' => 'Overdue',
                    ] as $value => $label)
                        <x-mine.dropdown.item>
                            <div class="w-full py-2 px-4 flex items-center gap-2 hover:cursor-pointer"
                                @click="$wire.set('status_filter', '{{ $value }}')">
                                <p
                                    class="text-sm font-medium flex-1 {{ $status_filter === $value ? 'mine-text-link' : 'mine-text-secondary' }}">
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
                <x-mine.dropdown.content class="mt-1!">
                    <div class="flex justify-center w-80">
                        <x-mine.calendar wire:model.live="range_filter" :card="false" />
                    </div>
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
    </div>
    <div class="md:flex md:gap-6">
        <div class="grid grid-cols-1 gap-4 flex-1">
            @forelse ($this->plans as $plan)
                @if ($plan->done)
                    <div wire:key="plan-{{ $plan->id }}"
                        class="mine-card-interactive flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8">
                        <div class="flex justify-between items-center gap-4 mb-6 min-w-0">
                            <div class="flex min-w-0 flex-1">
                                <div
                                    class="shrink-0 mine-badge-secondary rounded-xl size-16 flex justify-center items-center mr-4">
                                    <x-mine.icon name="trophy" class="size-8" />
                                </div>
                                <div class="flex flex-col justify-between gap-2 min-w-0">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <h1 class="text-md font-bold mine-text-primary truncate min-w-0">{{ $plan->name }}</h1>
                                        <div
                                            class="mine-badge-secondary rounded-lg h-7 flex justify-center items-center px-2 shrink-0">
                                            <p class="text-[14px] font-medium">Completed</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-medium mine-text-secondary min-w-0 line-clamp-2">
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
                                        <x-mine.dropdown.item>
                                            <div
                                                class="w-full py-2 px-4 flex items-center gap-2 mine-text-link hover:cursor-pointer">
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
                                <div
                                    class="mine-badge-secondary rounded-lg size-8 flex justify-center items-center mr-3 shrink-0">
                                    <x-mine.icon name="calendar" variant="micro" />
                                </div>
                                <p class="text-sm font-medium mine-text-secondary">{{ $plan->start_date->format('Y-m-d') }}</p>
                                <div class="mine-text-secondary flex justify-center items-center mx-2">
                                    <x-mine.icon name="arrow-long-right" variant="mini" />
                                </div>
                                <p class="text-sm font-medium mine-text-secondary">{{ $plan->finish_date->format('Y-m-d') }}</p>
                            </div>
                        </div>
                        <div class="pt-4 pb-3 opacity-50">
                            <x-mine.separator />
                        </div>
                        <div class="flex justify-between items-center gap-6">
                            <div class="flex flex-col justify-between flex-2">
                                <h2 class="text-sm font-medium mine-text-primary mb-2">Progress</h2>
                                <div class="flex items-center justify-between mb-2">
                                    <h2 class="text-xl font-medium mine-text-secondary-accent">
                                        {{ $plan->tasks_count > 0 ? (int) round($plan->tasks_done_count / $plan->tasks_count * 100) : 100 }}%
                                    </h2>
                                    <p class="sm:hidden text-[14px] font-medium mine-text-secondary">
                                        <span
                                            class="mine-text-secondary-accent text-md font-bold">{{ $plan->tasks_done_count }}</span>
                                        of <span class="mine-text-primary text-md font-bold">{{ $plan->tasks_count }}</span>
                                        completed
                                    </p>
                                </div>
                                <x-mine.progress total="{{ $plan->tasks_count ?: 1 }}"
                                    progress="{{ $plan->tasks_count ? $plan->tasks_done_count : 1 }}" variant="gray"
                                    class="h-3" />
                            </div>
                            <div class="hidden sm:flex flex-col items-end flex-1 gap-2">
                                <div
                                    class="mine-badge-secondary rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                                    <x-mine.icon name="check" variant="micro" />
                                    <p class="text-[14px] font-medium ml-2">{{ $plan->tasks_done_count }} completed</p>
                                </div>
                                <div
                                    class="mine-badge-secondary rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
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
                            <x-mine.button type="button" class="mine-btn-outline-secondary"
                                @click.stop="$wire.set('reopening_id', {{ $plan->id }}, false); $dispatch('open-modal', { id: 'reopen-plan-confirmation' })">
                                <div class="flex justify-center items-center gap-1 text-sm font-medium">
                                    <x-mine.icon name="arrow-path" variant="micro" />
                                    <p>Reopen Plan</p>
                                </div>
                            </x-mine.button>
                        </div>
                    </div>
                @elseif ($plan->finish_date->isPast())
                    <div wire:key="plan-{{ $plan->id }}"
                        class="mine-card-interactive flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8">
                        <div class="flex justify-between items-center gap-4 mb-6 min-w-0">
                            <div class="flex min-w-0 flex-1">
                                <div
                                    class="shrink-0 mine-badge-danger rounded-xl size-16 flex justify-center items-center mr-4">
                                    <x-mine.icon name="bell-alert" class="size-8" />
                                </div>
                                <div class="flex flex-col justify-between gap-2 min-w-0">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <h1 class="text-md font-bold mine-text-primary min-w-0 truncate">{{ $plan->name }}</h1>
                                        <div class="mine-badge-danger rounded-lg h-7 flex justify-center items-center px-2">
                                            <p class="text-[14px] font-medium">Overdue</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-medium mine-text-secondary min-w-0 line-clamp-2">
                                        {{ $plan->description ?: 'No description' }}</p>
                                </div>
                            </div>
                            <div class="flex justify-end items-start h-full">
                                <x-mine.dropdown group="plan-actions">
                                    <x-mine.dropdown.trigger>
                                        <div class="mine-btn-icon p-2 rounded-xl">
                                            <x-mine.icon name="ellipsis-horizontal" class="size-5" />
                                        </div>
                                    </x-mine.dropdown.trigger>
                                    <x-mine.dropdown.content>
                                        <x-mine.dropdown.item>
                                            <div
                                                class="w-full py-2 px-4 flex items-center gap-2 mine-text-link hover:cursor-pointer">
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
                            <div class="flex items-center">
                                <div class="mine-badge-danger rounded-lg size-8 flex justify-center items-center mr-3">
                                    <x-mine.icon name="calendar" variant="micro" />
                                </div>
                                <p class="text-sm font-medium mine-text-secondary">{{ $plan->start_date->format('Y-m-d') }}</p>
                                <div class="mine-text-secondary flex justify-center items-center mx-2">
                                    <x-mine.icon name="arrow-long-right" variant="mini" />
                                </div>
                                <p class="text-sm font-medium mine-text-secondary">{{ $plan->finish_date->format('Y-m-d') }}</p>
                            </div>
                            <div class="flex justify-end items-center">
                                <div
                                    class="mine-badge-danger rounded-lg h-8 flex justify-center items-center pr-1.5 pl-2 sm:pr-2 sm:pl-1.5 sm:min-w-40">
                                    <p class="sm:hidden text-[14px] font-medium mr-2">
                                        {{ (int) -now()->diffInDays($plan->finish_date) }}</p>
                                    <x-mine.icon name="clock" variant="micro" class="sm:hidden " />
                                    <x-mine.icon name="clock" variant="micro" class="hidden sm:inline " />
                                    <p class="hidden sm:inline text-[14px] font-medium ml-2">
                                        {{ (int) -now()->diffInDays($plan->finish_date) }} <span class="hidden sm:inline">days
                                            overdue</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 pb-3 opacity-50">
                            <x-mine.separator />
                        </div>
                        <div class="flex justify-between items-center gap-6">
                            <div class="flex flex-col justify-between flex-2">
                                <h2 class="text-sm font-medium mine-text-primary mb-2">Progress</h2>
                                <div class="flex items-center justify-between mb-2">
                                    <h2 class="text-xl font-medium mine-text-error">
                                        {{ ($plan->tasks_count > 0) ? (int) round($plan->tasks_done_count / $plan->tasks_count * 100) : 0 }}%
                                    </h2>
                                    <p class="sm:hidden text-[14px] font-medium mine-text-secondary">
                                        <span class="mine-text-error text-md font-bold">{{ $plan->tasks_done_count }}</span> of
                                        <span class="mine-text-primary text-md font-bold">{{ $plan->tasks_count }}</span>
                                        completed
                                    </p>
                                </div>
                                <x-mine.progress total="100"
                                    progress="{{ ($plan->tasks_count > 0) ? (int) round($plan->tasks_done_count / $plan->tasks_count * 100) : 0 }}"
                                    variant="danger" class="h-3" />
                            </div>
                            <div class="hidden sm:flex flex-col items-end flex-1 gap-2">
                                <div
                                    class="mine-badge-secondary rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                                    <x-mine.icon name="check" variant="micro" />
                                    <p class="text-[14px] font-medium ml-2">{{ $plan->tasks_done_count }} completed</p>
                                </div>
                                <div
                                    class="mine-badge-danger rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
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
                            <x-mine.button type="button" class="mine-btn-outline-danger">
                                <div class="flex justify-center items-center gap-1 text-sm font-md">
                                    <x-mine.icon name="plus" variant="micro" />
                                    <p>Add Task</p>
                                </div>
                            </x-mine.button>
                            <x-mine.button type="button" class="mine-btn-danger"
                                @click.stop="$wire.set('completing_id', {{ $plan->id }}, false); $dispatch('open-modal', { id: 'complete-plan-confirmation' })">
                                <div class="flex justify-center items-center gap-1 text-sm font-medium">
                                    <x-mine.icon name="check" variant="micro" />
                                    <p class="hidden sm:block">Mark as Completed</p>
                                    <p class="sm:hidden">Complete</p>
                                </div>
                            </x-mine.button>
                        </div>
                    </div>
                @else
                    <div wire:key="plan-{{ $plan->id }}"
                        class="mine-card-interactive flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8">
                        <div class="flex justify-between items-center gap-10 mb-6 min-w-0">
                            <div class="flex min-w-0 flex-1">
                                <div
                                    class="shrink-0 mine-badge-primary rounded-xl size-16 flex justify-center items-center mr-4">
                                    <x-mine.icon name="rocket-launch" class="size-8" />
                                </div>
                                <div class="flex flex-col justify-between gap-2 pb-1 min-w-0">
                                    <div class="flex items-center gap-4 min-w-0">
                                        <h1 class="text-md font-bold mine-text-primary truncate min-w-0">{{ $plan->name }}</h1>
                                        <div class="mine-badge-primary rounded-lg h-7 flex justify-center items-center px-2">
                                            <p class="text-[14px] font-medium">Active</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-medium mine-text-secondary min-w-0 line-clamp-2">
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
                                        <x-mine.dropdown.item>
                                            <div
                                                class="w-full py-2 px-4 flex items-center gap-2 mine-text-link hover:cursor-pointer">
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
                            <div class="flex items-center">
                                <div class="mine-badge-primary rounded-lg size-8 flex justify-center items-center mr-3">
                                    <x-mine.icon name="calendar" variant="micro" />
                                </div>
                                <p class="text-sm font-medium mine-text-secondary">{{ $plan->start_date->format('Y-m-d') }}</p>
                                <div class="mine-text-secondary flex justify-center items-center mx-2">
                                    <x-mine.icon name="arrow-long-right" variant="mini" />
                                </div>
                                <p class="text-sm font-medium mine-text-secondary">{{ $plan->finish_date->format('Y-m-d') }}</p>
                            </div>
                            <div class="flex justify-end items-center">
                                <div
                                    class="mine-badge-primary rounded-lg h-8 flex justify-center items-center pr-1.5 pl-2 sm:pr-2 sm:pl-1.5 sm:min-w-35">
                                    <p class="sm:hidden text-[14px] font-medium mr-2">
                                        {{ max(0, (int) now()->diffInDays($plan->finish_date)) }}</p>
                                    <x-mine.icon name="clock" variant="micro" class="sm:hidden " />
                                    <x-mine.icon name="clock" variant="micro" class="hidden sm:inline " />
                                    <p class="hidden sm:inline text-[14px] font-medium ml-2">
                                        {{ max(0, (int) now()->diffInDays($plan->finish_date)) }} <span
                                            class="hidden sm:inline">days left</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 pb-3 opacity-50">
                            <x-mine.separator />
                        </div>
                        <div class="flex justify-between items-center gap-6">
                            <div class="flex flex-col justify-between flex-2">
                                <h2 class="text-sm font-medium mine-text-primary mb-2">Progress</h2>
                                <div class="flex items-center justify-between mb-2">
                                    <h2 class="text-xl font-medium mine-text-link">
                                        {{ ($plan->tasks_count > 0) ? (int) round($plan->tasks_done_count / $plan->tasks_count * 100) : 0 }}%
                                    </h2>
                                    <p class="sm:hidden text-[14px] font-medium mine-text-secondary">
                                        <span class="mine-text-link text-md font-bold">{{ $plan->tasks_done_count }}</span> of
                                        <span class="mine-text-primary text-md font-bold">{{ $plan->tasks_count }}</span>
                                        completed
                                    </p>
                                </div>
                                <x-mine.progress total="100"
                                    progress="{{ ($plan->tasks_count > 0) ? (int) round($plan->tasks_done_count / $plan->tasks_count * 100) : 0 }}"
                                    class="h-3" />
                            </div>
                            <div class="hidden sm:flex flex-col items-end flex-1 gap-2">
                                <div
                                    class="mine-badge-primary rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                                    <x-mine.icon name="check" variant="micro" />
                                    <p class="text-[14px] font-medium ml-2">{{ $plan->tasks_done_count }} completed</p>
                                </div>
                                <div
                                    class="mine-badge-secondary rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
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
                            <x-mine.button type="button" class="mine-btn-outline-primary">
                                <div class="flex justify-center items-center gap-1 text-sm font-md">
                                    <x-mine.icon name="plus" variant="micro" />
                                    <p>Add Task</p>
                                </div>
                            </x-mine.button>
                            <x-mine.button type="button" class="mine-btn-primary"
                                @click.stop="$wire.set('completing_id', {{ $plan->id }}, false); $dispatch('open-modal', { id: 'complete-plan-confirmation' })">
                                <div class="flex justify-center items-center gap-1 text-sm font-medium">
                                    <x-mine.icon name="check" variant="micro" />
                                    <p class="hidden sm:block">Mark as Completed</p>
                                    <p class="sm:hidden">Complete</p>
                                </div>
                            </x-mine.button>
                        </div>
                    </div>
                @endif
            @empty
                @if($this->search or $this->range_filter or $this->status_filter !== 'all')
                    <div class="col-span-full text-center py-12">
                        <x-mine.icon name="magnifying-glass" class="size-12 mx-auto mb-3 mine-text-secondary" />
                        <p class="mine-text-secondary text-sm font-medium">No plans found.</p>
                    </div>
                @else
                    <div class="col-span-full text-center py-12">
                        <x-mine.icon name="folder-open" class="size-12 mx-auto mb-3 mine-text-secondary" />
                        <p class="mine-text-secondary text-sm font-medium">No plans yet.</p>
                    </div>
                @endif
            @endforelse
        </div>
        <div class="hidden md:flex justify-center w-80">
            <x-mine.calendar wire:model.live="range_filter" />
        </div>
    </div>
    <div class="mt-6 w-full">
        {{ $this->plans->links(data: ['scrollTo' => false]) }}
    </div>

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