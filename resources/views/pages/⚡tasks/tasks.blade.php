<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6">
        <h1 class="font-bold text-md mine-text-primary">My Tasks</h1>
    </div>
    @island(name: 'tasks-content', always: true)
    <div class="flex flex-col gap-2 sm:gap-4 mb-6">
        <div class="w-full flex items-center justify-between gap-2 sm:gap-4">
            <div class="w-full">
                <x-mine.input wire:model.live.debounce.200ms="search" placeholder="Search tasks..."
                    leftIcon="magnifying-glass" />
            </div>
            <x-mine.modal.trigger id="add-task-form">
                <x-mine.button type="button" class="mine-btn-outline-primary pl-3! pr-3! sm:pr-4!">
                    <div class="flex justify-center items-center gap-2">
                        <x-mine.icon name="plus" class="inline" variant="micro" />
                        <p class="text-sm font-medium hidden sm:inline"><span
                                class="hidden sm:inline md:hidden">New</span><span class="hidden md:inline">New
                                Task</span>
                        </p>
                    </div>
                </x-mine.button>
            </x-mine.modal.trigger>
            <x-mine.dropdown group="task-filter">
                <x-mine.dropdown.trigger as="div">
                    <div
                        class="w-full mine-btn-outline-primary flex items-center h-11 px-4 sm:pl-3! rounded-xl cursor-pointer select-none">
                        <x-mine.icon name="arrow-long-up" class="inline -mr-1 -ml-2.5 sm:ml-0" variant="micro" />
                        <x-mine.icon name="arrow-long-down" class="inline -ml-1 -mr-2.5 sm:mr-0" variant="micro" />
                        <p class="text-sm font-medium hidden sm:inline sm:pl-2"><span
                                class="hidden sm:inline md:hidden">Sort</span><span class="hidden md:inline">Sort
                                By</span>
                        </p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1!">
                    @foreach ([
                            'state' => 'State',
                            'load' => 'Load',
                            'latest' => 'Latest',
                            'date' => 'Date',
                            'priority' => 'Priority',
                        ] as $value => $label)
                        <x-mine.dropdown.item>
                            <div class="w-full py-2 px-4 flex items-center gap-2 hover:cursor-pointer"
                                @click="$wire.$island('tasks-content').$set('sort', '{{ $value }}')">
                                <p
                                    class="text-sm font-medium {{ $sort === $value ? 'mine-text-link' : 'mine-text-secondary' }}">
                                    {{ $label }}
                                </p>
                                @if($sort === $value)
                                    <x-mine.icon variant="micro" name="check" class="size-4 mine-text-link" />
                                @endif
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="task-filter" class="w-full md:w-auto hidden md:flex">
                <x-mine.dropdown.trigger as="div" class="w-full md:w-auto">
                    <div
                        class="w-full md:w-auto mine-btn-outline-primary flex items-center justify-center gap-2 h-11 px-3 sm:pr-4! rounded-xl cursor-pointer select-none">
                        <x-mine.icon name="funnel" class="" variant="micro" />
                        <p class="text-sm font-medium">
                            Filter Task
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
                                @click="$wire.$island('tasks-content').$set('status_filter', '{{ $value }}')">
                                <p
                                    class="text-sm font-medium {{ $status_filter === $value ? 'mine-text-link' : 'mine-text-secondary' }}">
                                    {{ $label }}
                                </p>
                                @if($status_filter === $value)
                                    <x-mine.icon variant="micro" name="check" class="size-4 mine-text-link" />
                                @endif
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </div>
        <div class="w-full flex justify-between gap-2">
            <x-mine.dropdown group="task-filter" class="w-full md:w-auto md:hidden">
                <x-mine.dropdown.trigger as="div" class="w-full md:w-auto">
                    <div
                        class="w-full md:w-auto mine-btn-outline-primary flex items-center justify-center gap-2 h-11 px-3 sm:pr-4! rounded-xl cursor-pointer select-none">
                        <x-mine.icon name="funnel" class="" variant="micro" />
                        <p class="text-sm font-medium hidden sm:block">
                            Filter <span class="hidden sm:inline">Tasks</span>
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
                                @click="$wire.$island('tasks-content').$set('status_filter', '{{ $value }}')">
                                <p
                                    class="text-sm font-medium {{ $status_filter === $value ? 'mine-text-link' : 'mine-text-secondary' }}">
                                    {{ $label }}
                                </p>
                                @if($status_filter === $value)
                                    <x-mine.icon variant="micro" name="check" class="size-4 mine-text-link" />
                                @endif
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="task-filter" multiple model="category_filter" class="w-full">
                <x-mine.dropdown.trigger as="div" class="w-full">
                    <div
                        class="w-full mine-btn-outline-primary flex items-center justify-center gap-2 h-11 px-3 sm:pr-4! rounded-xl cursor-pointer select-none">
                        <x-mine.icon name="folder" class="" variant="micro" />
                        <p class="text-sm font-medium hidden sm:block"">
                            Category
                        </p>
                        @if(count($category_filter) > 0)
                            <span class=" mine-badge-primary text-xs font-semibold rounded-full px-1.5 py-0.5">
                            {{ count($category_filter) }}
                            </span>
                        @endif
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1! max-h-60 overflow-y-auto mine-scrollbar" placement="bottom-start">
                    @foreach ($this->categories as $category)
                        <x-mine.dropdown.item :value="$category->id">
                            <p class="w-full flex py-2 pr-4 pl-3 text-sm font-medium mine-text-primary cursor-pointer">
                                {{ $category->name }}
                            </p>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="task-filter" multiple model="plan_filter" class="w-full">
                <x-mine.dropdown.trigger as="div" class="w-full">
                    <div
                        class="w-full mine-btn-outline-primary flex items-center justify-center gap-2 h-11 px-3 sm:pr-4! rounded-xl cursor-pointer select-none">
                        <x-mine.icon name="rocket-launch" class="" variant="micro" />
                        <p class="text-sm font-medium  hidden sm:block"">
                            Plan
                        </p>
                        @if(count($plan_filter) > 0)
                            <span class=" mine-badge-primary text-xs font-semibold rounded-full px-1.5 py-0.5">
                            {{ count($plan_filter) }}
                            </span>
                        @endif
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1! max-h-60 overflow-y-auto mine-scrollbar">
                    @foreach ($this->plans as $plan)
                        <x-mine.dropdown.item :value="$plan->id">
                            <p class="w-full flex py-2 pr-4 pl-3 text-sm font-medium mine-text-primary cursor-pointer">
                                {{ $plan->name }}
                            </p>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
            <x-mine.dropdown group="task-filter" class="md:hidden w-full">
                <x-mine.dropdown.trigger as="div" class=" w-full">
                    <div
                        class="w-full mine-btn-outline-primary flex items-center justify-center gap-2 h-11 px-3 sm:pr-4! rounded-xl cursor-pointer select-none">
                        <x-mine.icon name="calendar" class="" variant="micro" />
                        <p class="text-sm font-medium hidden sm:block">
                            Date <span class="hidden sm:inline">Range</span>
                        </p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1! flex justify-center">
                    <div class="flex justify-center w-80">
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
                    <svg class="size-8 animate-spin mine-text-secondary" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" role="status" aria-label="Loading">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                </div>
            </div>
            <div wire:loading.delay.short.class="opacity-40" class="transition-opacity">
                <div class="grid grid-cols-1 gap-4">
                    @forelse ($this->tasks as $task)
                        @php
                            $status = $task->done ? 'completed' : ($task->task_date->isPast() ? 'overdue' : 'active');

                            $badge = match ($status) {
                                'completed' => 'mine-badge-secondary',
                                'overdue' => 'mine-badge-danger',
                                'active' => 'mine-badge-primary',
                            };

                            $descText = match ($status) {
                                'completed' => 'mine-text-secondary',
                                'overdue' => 'mine-text-danger',
                                'active' => 'mine-text-primary',
                            };

                            $actionButton = match ($status) {
                                'completed' => 'mine-btn-outline-secondary',
                                'overdue' => 'mine-btn-outline-danger',
                                'active' => 'mine-btn-outline-primary',
                            };
                        @endphp
                        <div wire:key="task-{{ $task->id }}"
                            class="self-start mine-card-interactive flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8">
                            <div class="flex justify-between gap-4 mb-6 min-w-0">
                                <div class="flex min-w-0 flex-1">
                                    <div
                                        class="rounded-xl size-14 shrink-0 {{ $badge }} flex justify-center items-center mr-4">
                                        @if ($status === 'completed')
                                            <x-mine.icon name="check-circle" class="size-8" variant="solid" />
                                        @else
                                            <div class="size-7 rounded-full border-2"></div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col justify-between gap-2 min-w-0">
                                        <div class="flex items-center gap-4 min-w-0">
                                            <h1
                                                class="text-md font-bold mine-text-primary truncate min-w-0 {{ $status === 'completed' ? 'line-through' : '' }}">
                                                {{ $task->title }}
                                            </h1>
                                            <div
                                                class="shrink-0 {{ $badge }} rounded-lg h-7 flex items-center justify-center px-2">
                                                <p class="text-[14px] font-medium">{{ ucfirst($status) }}</p>
                                            </div>
                                        </div>
                                        <p
                                            class="text-sm font-medium {{ $descText }} line-clamp-2 min-w-0 {{ $status === 'completed' ? 'line-through' : '' }}">
                                            {{ $task->description ?: 'No description' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-end items-start h-full shrink-0">
                                    <x-mine.dropdown group="task-actions">
                                        <x-mine.dropdown.trigger>
                                            <div class="mine-btn-icon p-2 rounded-xl">
                                                <x-mine.icon name="ellipsis-horizontal" class="size-5" />
                                            </div>
                                        </x-mine.dropdown.trigger>
                                        <x-mine.dropdown.content>
                                            <x-mine.dropdown.item>
                                                <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-link hover:cursor-pointer"
                                                    @click.stop="$wire.set('editing_id', {{ $task->id }}, false);
                                            $wire.set('edit_title', @js($task->title), false);
                                            $wire.set('edit_description', @js($task->description), false);
                                            $wire.set('edit_date', @js($task->task_date->format('Y-m-d')), false);
                                            $wire.set('edit_estimated_minutes', {{ $task->estimated_minutes }}, false);
                                            $wire.set('edit_alarm_days', {{ $task->day_before_alarm }}, false);
                                            $wire.set('edit_priority', @js($task->priority->value), false);
                                            $wire.set('edit_category_id', {{ $task->category_id }}, false);
                                            $wire.set('edit_plan_id', @js($task->plan_id), false);
                                            $dispatch('open-modal', { id: 'edit-task-form' })">
                                                    <x-mine.icon name="pencil" class="size-4" variant="solid" />
                                                    <p class="text-sm font-medium">Edit</p>
                                                </div>
                                            </x-mine.dropdown.item>
                                            <x-mine.dropdown.item destructive>
                                                <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-error hover:cursor-pointer"
                                                    @click.stop="$wire.set('deleting_id', {{ $task->id }}, false);
                                            $dispatch('open-modal', { id: 'delete-task-confirmation' })">
                                                    <x-mine.icon name="trash" class="size-4" variant="solid" />
                                                    <p class="text-sm font-medium">Delete</p>
                                                </div>
                                            </x-mine.dropdown.item>
                                        </x-mine.dropdown.content>
                                    </x-mine.dropdown>
                                </div>
                            </div>
                            <div class="flex flex-col justify-between">
                                <div class="flex flex-col gap-2 mt-3">
                                    <div class="flex gap-2">
                                        <div class="{{ $badge }} rounded-lg h-7 flex items-center px-2.5 justify-center">
                                            <x-mine.icon name="folder" variant="micro" class="mr-1.5" />
                                            <p class="text-[13px] font-medium">{{ $task->category->name }}</p>
                                        </div>
                                        @if ($task->plan)
                                            <div class="{{ $badge }} rounded-lg h-7 flex items-center px-2.5 justify-center">
                                                <x-mine.icon name="rocket-launch" variant="micro" class="mr-1.5" />
                                                <p class="text-[13px] font-medium">{{ $task->plan->name }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="{{ $badge }} rounded-lg h-7 flex items-center px-2.5 justify-center">
                                            <x-mine.icon name="calendar" variant="micro" class="mr-1.5" />
                                            <p class="text-[13px] font-medium">{{ $task->task_date->format('Y-m-d') }}</p>
                                        </div>
                                        <div class="{{ $badge }} rounded-lg h-7 flex items-center px-2.5 justify-center">
                                            <x-mine.icon name="clock" variant="micro" class="mr-1.5" />
                                            <p class="text-[13px] font-medium">{{ $task->estimated_minutes }} min</p>
                                        </div>
                                        <div class="{{ $badge }} rounded-lg h-7 flex items-center px-2.5 justify-center">
                                            <x-mine.icon name="fire" variant="micro" class="mr-1.5" />
                                            <p class="text-[13px] font-medium capitalize">{{ $task->priority->value }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-4 opacity-50">
                                    <x-mine.separator />
                                </div>
                                <div class="flex items-end">
                                    @if ($status === 'completed')
                                        <x-mine.button type="button" class="{{ $actionButton }}"
                                            @click.stop="$wire.set('reopening_id', {{ $task->id }}, false); $dispatch('open-modal', { id: 'reopen-task-confirmation' })">
                                            <div class="flex justify-center items-center gap-1 text-sm font-medium">
                                                <x-mine.icon name="arrow-path" variant="micro" />
                                                <p>Reopen Task</p>
                                            </div>
                                        </x-mine.button>
                                    @else
                                        <x-mine.button type="button" class="{{ $actionButton }}"
                                            @click.stop="$wire.set('completing_id', {{ $task->id }}, false); $dispatch('open-modal', { id: 'complete-task-confirmation' })">
                                            <div class="flex justify-center items-center gap-1 text-sm font-medium">
                                                <x-mine.icon name="check" variant="micro" />
                                                <p>Complete Task</p>
                                            </div>
                                        </x-mine.button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        @if($this->hasActiveFilters)
                            <div class="col-span-full text-center py-12">
                                <x-mine.icon name="magnifying-glass" class="size-12 mx-auto mb-3 mine-text-secondary" />
                                <p class="mine-text-secondary text-sm font-medium">No tasks found.</p>
                            </div>
                        @else
                            <div class="col-span-full text-center py-12">
                                <x-mine.icon name="clipboard" class="size-12 mx-auto mb-3 mine-text-secondary" />
                                <p class="mine-text-secondary text-sm font-medium">No tasks yet.</p>
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
        {{ $this->tasks->links(data: ['scrollTo' => false]) }}
    </div>
    @endisland

    <x-mine.modal id="add-task-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Add new task</h2>
                <button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($add_success === 'created')
                <div class="mb-4">
                    <x-mine.alert variant="success" title="Task created.">Your new task has been added.</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'invalid_category')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Invalid category.">Please choose a valid category.</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'invalid_plan')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Invalid plan.">Please choose a valid plan.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="addTask">
                <x-mine.input label="Task Title" wire:model="add_title" placeholder="Enter task title" />
                <x-mine.datepicker mode="single" position="bottom-end" wire:model="add_date" label="Task Date" />
                <div class="grid grid-cols-2 gap-4">
                    <x-mine.input label="Estimated Minutes" wire:model="add_estimated_minutes" type="number" placeholder="e.g. 30" />
                    <x-mine.input label="Days before Alarm" wire:model="add_alarm_days" type="number" placeholder="e.g. 1" />
                </div>
                <x-mine.select label="Priority" wire:model="add_priority" placeholder="Select priority">
                    <x-mine.select.option value="low">Low</x-mine.select.option>
                    <x-mine.select.option value="medium">Medium</x-mine.select.option>
                    <x-mine.select.option value="high">High</x-mine.select.option>
                </x-mine.select>
                <x-mine.select label="Category" wire:model="add_category_id" placeholder="Select category" searchable>
                    @forelse ($this->categories as $category)
                        <x-mine.select.option value="{{ $category->id }}">{{ $category->name }}</x-mine.select.option>
                    @empty
                        <x-mine.select.option value="">No categories yet</x-mine.select.option>
                    @endforelse
                </x-mine.select>
                <x-mine.select label="Plan" wire:model="add_plan_id" placeholder="Select plan" searchable>
                    <x-mine.select.option value="">No plan</x-mine.select.option>
                    @foreach ($this->plans as $plan)
                        <x-mine.select.option value="{{ $plan->id }}">{{ $plan->name }}</x-mine.select.option>
                    @endforeach
                </x-mine.select>
                <div>
                    <x-mine.textarea label="Description" wire:model="add_description" placeholder="Enter description" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="addTask" class="mine-btn-primary">Add</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-task-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Edit task</h2>
                <button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($edit_success === 'updated')
                <div class="mb-4">
                    <x-mine.alert variant="success" title="Task updated.">Your task has been updated.</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'invalid_category')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Invalid category.">Please choose a valid category.</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'invalid_plan')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Invalid plan.">Please choose a valid plan.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editTask">
                <x-mine.input label="Task Title" wire:model="edit_title" placeholder="Enter task title" />
                <x-mine.datepicker mode="single" position="bottom-end" wire:model="edit_date" label="Task Date" />
                <div class="grid grid-cols-2 gap-4">
                    <x-mine.input label="Estimated Minutes" wire:model="edit_estimated_minutes" type="number" />
                    <x-mine.input label="Days before Alarm" wire:model="edit_alarm_days" type="number" />
                </div>
                <x-mine.select label="Priority" wire:model="edit_priority" placeholder="Select priority">
                    <x-mine.select.option value="low">Low</x-mine.select.option>
                    <x-mine.select.option value="medium">Medium</x-mine.select.option>
                    <x-mine.select.option value="high">High</x-mine.select.option>
                </x-mine.select>
                <x-mine.select label="Category" wire:model="edit_category_id" placeholder="Select category" searchable>
                    @forelse ($this->categories as $category)
                        <x-mine.select.option value="{{ $category->id }}">{{ $category->name }}</x-mine.select.option>
                    @empty
                        <x-mine.select.option value="">No categories yet</x-mine.select.option>
                    @endforelse
                </x-mine.select>
                <x-mine.select label="Plan" wire:model="edit_plan_id" placeholder="Select plan" searchable>
                    <x-mine.select.option value="">No plan</x-mine.select.option>
                    @foreach ($this->plans as $plan)
                        <x-mine.select.option value="{{ $plan->id }}">{{ $plan->name }}</x-mine.select.option>
                    @endforeach
                </x-mine.select>
                <div>
                    <x-mine.textarea label="Description" wire:model="edit_description"
                        placeholder="Enter description" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="editTask" class="mine-btn-primary">Save</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-task-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Delete task</h2>
                <button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            <div class="mb-4">
                <x-mine.alert variant="warning" title="Are you sure?">This action cannot be undone.</x-mine.alert>
            </div>

            <form class="flex flex-col gap-4" wire:submit="deleteTask">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="deleteTask" class="mine-btn-danger">Delete</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="complete-task-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Complete task</h2>
                <button type="button" @click="close(); $wire.cancelComplete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($complete_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Are you sure?">This will mark the task as
                        completed.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="completeTask">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelComplete()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="completeTask" class="mine-btn-primary">Complete</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="reopen-task-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Reopen task</h2>
                <button type="button" @click="close(); $wire.cancelReopen()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($reopen_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Are you sure?">This task will be moved back to
                        active.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="reopenTask">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelReopen()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="reopenTask" class="mine-btn-primary">Reopen</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>