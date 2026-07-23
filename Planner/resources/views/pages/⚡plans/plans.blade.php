<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6 ml-2">
        <h1 class="font-bold text-md mine-text-primary">My Plans</h1>
    </div>
    <div class=" flex items-center justify-between gap-4 mb-6">
        <div class="w-full">
            <x-mine.input wire:model.live.debounce.200ms="search" placeholder="Search plans..."
                leftIcon="magnifying-glass" />
        </div>
        <x-mine.modal.trigger id="add-plan-form">
            <x-mine.button type="button" class=" mine-btn-outline-primary">
                <div class="flex justify-center items-center gap-2">
                    <x-mine.icon name="plus" class="inline" />
                    <p><span class="sm:hidden">New</span><span class="hidden sm:inline">New Plan</span></p>
                </div>
            </x-mine.button>
        </x-mine.modal.trigger>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
        @forelse ($this->plans as $plan)
            <div wire:key="plan-{{ $plan->id }}" class="mine-card-interactive flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8">
                <div class="flex justify-between items-center gap-4 mb-6">
                    <div class="flex">
                        <div class="avatar-secondary rounded-full size-16 flex justify-center items-center mr-4">
                            <x-mine.icon name="trophy" class="size-8" />
                        </div>
                        <div class="flex flex-col justify-between gap-2">
                            <div class="flex items-center gap-4">
                                <h1 class="text-md font-bold mine-text-primary">{{ $plan->name }}</h1>
                                <div class="avatar-secondary rounded-lg h-7 flex justify-center items-center px-2">
                                    <p class="text-[14px] font-medium">Completed</p>
                                </div>
                            </div>
                            <p class="text-sm font-medium mine-text-secondary">{{ $plan->description ?: 'No description.' }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end items-start h-full">
                        <div class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
                            <x-mine.icon name="ellipsis-horizontal" class="size-6" />
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <div class="flex items-center">
                        <div class="avatar-secondary rounded-lg size-8 flex justify-center items-center mr-3">
                            <x-mine.icon name="calendar" variant="micro" />
                        </div>
                        <p class="text-sm font-medium mine-text-primary">{{ $plan->start_date->format('F j, Y') }}</p>
                        <div class="mine-text-secondary flex justify-center items-center mx-2">
                            <x-mine.icon name="arrow-long-right" variant="mini" />
                        </div>
                        <p class="text-sm font-medium mine-text-primary">{{ $plan->finish_date->format('F j, Y') }}</p>
                    </div>
                    <div class="flex justify-end items-center">
                        <div class="avatar-secondary rounded-lg h-8 flex justify-center items-center pr-1.5 pl-2 sm:pr-2 sm:pl-1.5 sm:min-w-35">
                            <p class="sm:hidden text-[14px] font-medium mr-2">Done</span></p>
                            <x-mine.icon name="check" variant="micro" class="sm:hidden " />
                            <x-mine.icon name="check" variant="micro" class="hidden sm:inline " />
                            <p class="hidden sm:inline text-[14px] font-medium ml-2">Completed on {{ 'July 30, 2026' }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="py-4 opacity-60">
                    <x-mine.separator />
                </div>
                <div class="flex justify-between items-center gap-6">
                    <div class="flex flex-col justify-between flex-2">
                        <h2 class="text-sm font-medium mine-text-primary mb-2">Progress</h2>
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-xl font-medium mine-text-secondary">{{ 40 }}%</h2>
                            <p class="sm:hidden text-[14px] font-medium mine-text-secondary">
                                <span class="mine-text-secondary text-md font-bold">{{ 4 }}</span> of <span class="mine-text-primary text-md font-bold">{{ 10 }}</span> completed
                            </p>
                        </div>
                        <x-mine.progress total="100" progress="40" variant="gray" class="h-3" />
                    </div>
                    <div class="hidden sm:flex flex-col items-end flex-1 gap-2">
                        <div class="avatar-secondary rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                            <x-mine.icon name="check" variant="micro" />
                            <p class="text-[14px] font-medium ml-2">{{ 4 }} completed</p>
                        </div>
                        <div class="avatar-secondary rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                            <x-mine.icon name="x-mark" variant="micro" />
                            <p class="text-[14px] font-medium ml-2">{{ 6 }} remaining</p>
                        </div>
                    </div>
                </div>
                <div class="py-4 opacity-60">
                    <x-mine.separator />
                </div>
                <div class="flex gap-4 justify-between items-center">
                    <x-mine.button class="btn-outline-secondary">
                        <div class="flex justify-center items-center gap-1 text-sm font-md">
                            <x-mine.icon name="arrow-path" variant="micro" />
                            <p>Reopen Plan</p>
                        </div>
                    </x-mine.button>
                </div>
            </div>
            <div wire:key="plan-{{ $plan->id }}" class="mine-card-interactive flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8">
                <div class="flex justify-between items-center gap-4 mb-6">
                    <div class="flex">
                        <div class="avatar-danger rounded-full size-16 flex justify-center items-center mr-4">
                            <x-mine.icon name="calendar-days" class="size-8" />
                        </div>
                        <div class="flex flex-col justify-between gap-2">
                            <div class="flex items-center gap-4">
                                <h1 class="text-md font-bold mine-text-primary">{{ $plan->name }}</h1>
                                <div class="avatar-danger rounded-lg h-7 flex justify-center items-center px-2">
                                    <p class="text-[14px] font-medium">Overdue</p>
                                </div>
                            </div>
                            <p class="text-sm font-medium mine-text-secondary">{{ $plan->description ?: 'No description.' }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end items-start h-full">
                        <div class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
                            <x-mine.icon name="ellipsis-horizontal" class="size-6" />
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <div class="flex items-center">
                        <div class="avatar-danger rounded-lg size-8 flex justify-center items-center mr-3">
                            <x-mine.icon name="calendar" variant="micro" />
                        </div>
                        <p class="text-sm font-medium mine-text-primary">{{ $plan->start_date->format('F j, Y') }}</p>
                        <div class="mine-text-secondary flex justify-center items-center mx-2">
                            <x-mine.icon name="arrow-long-right" variant="mini" />
                        </div>
                        <p class="text-sm font-medium mine-text-primary">{{ $plan->finish_date->format('F j, Y') }}</p>
                    </div>
                    <div class="flex justify-end items-center">
                        <div class="avatar-danger rounded-lg h-8 flex justify-center items-center pr-1.5 pl-2 sm:pr-2 sm:pl-1.5 sm:min-w-40">
                            <p class="sm:hidden text-[14px] font-medium mr-2">{{ 10 }}</span></p>
                            <x-mine.icon name="clock" variant="micro" class="sm:hidden " />
                            <x-mine.icon name="clock" variant="micro" class="hidden sm:inline " />
                            <p class="hidden sm:inline text-[14px] font-medium ml-2">{{ 10 }} <span class="hidden sm:inline">days overdue</span></p>
                        </div>
                    </div>
                </div>
                <div class="py-4 opacity-60">
                    <x-mine.separator />
                </div>
                <div class="flex justify-between items-center gap-6">
                    <div class="flex flex-col justify-between flex-2">
                        <h2 class="text-sm font-medium mine-text-primary mb-2">Progress</h2>
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-xl font-medium mine-text-error">{{ 40 }}%</h2>
                            <p class="sm:hidden text-[14px] font-medium mine-text-secondary">
                                <span class="mine-text-error text-md font-bold">{{ 4 }}</span> of <span class="mine-text-primary text-md font-bold">{{ 10 }}</span> completed
                            </p>
                        </div>
                        <x-mine.progress total="100" progress="40" variant="danger" class="h-3" />
                    </div>
                    <div class="hidden sm:flex flex-col items-end flex-1 gap-2">
                        <div class="avatar-secondary rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                            <x-mine.icon name="check" variant="micro" />
                            <p class="text-[14px] font-medium ml-2">{{ 4 }} completed</p>
                        </div>
                        <div class="avatar-danger rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                            <x-mine.icon name="x-mark" variant="micro" />
                            <p class="text-[14px] font-medium ml-2">{{ 6 }} remaining</p>
                        </div>
                    </div>
                </div>
                <div class="py-4 opacity-60">
                    <x-mine.separator />
                </div>
                <div class="flex gap-4 justify-between items-center">
                    <x-mine.button class="mine-btn-outline-danger">
                        <div class="flex justify-center items-center gap-1 text-sm font-md">
                            <x-mine.icon name="plus" variant="micro" />
                            <p>Add Task</p>
                        </div>
                    </x-mine.button>
                    <x-mine.button class="mine-btn-danger">
                        <div class="flex justify-center items-center gap-1 text-sm font-medium">
                            <x-mine.icon name="check" variant="micro" />
                            <p class="hidden sm:block">Mark as Completed</p>
                            <p class="sm:hidden">Complete</p>
                        </div>
                    </x-mine.button>
                </div>
            </div>
            <div wire:key="plan-{{ $plan->id }}" class="mine-card-interactive flex flex-col px-4 sm:px-6 md:px-8 py-4 sm:pb-5 md:pb-6 sm:pt-6 md:pt-8">
                <div class="flex justify-between items-center gap-4 mb-6">
                    <div class="flex">
                        <div class="avatar rounded-full size-16 flex justify-center items-center mr-4">
                            <x-mine.icon name="rocket-launch" class="size-8" />
                        </div>
                        <div class="flex flex-col justify-between gap-2">
                            <div class="flex items-center gap-4">
                                <h1 class="text-md font-bold mine-text-primary">{{ $plan->name }}</h1>
                                <div class="avatar rounded-lg h-7 flex justify-center items-center px-2">
                                    <p class="text-[14px] font-medium">Active</p>
                                </div>
                            </div>
                            <p class="text-sm font-medium mine-text-secondary">{{ $plan->description ?: 'No description.' }}</p>
                        </div>
                    </div>
                    <div class="flex justify-end items-start h-full">
                        <div class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
                            <x-mine.icon name="ellipsis-horizontal" class="size-6" />
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <div class="flex items-center">
                        <div class="avatar rounded-lg size-8 flex justify-center items-center mr-3">
                            <x-mine.icon name="calendar" variant="micro" />
                        </div>
                        <p class="text-sm font-medium mine-text-primary">{{ $plan->start_date->format('F j, Y') }}</p>
                        <div class="mine-text-secondary flex justify-center items-center mx-2">
                            <x-mine.icon name="arrow-long-right" variant="mini" />
                        </div>
                        <p class="text-sm font-medium mine-text-primary">{{ $plan->finish_date->format('F j, Y') }}</p>
                    </div>
                    <div class="flex justify-end items-center">
                        <div class="avatar rounded-lg h-8 flex justify-center items-center pr-1.5 pl-2 sm:pr-2 sm:pl-1.5 sm:min-w-35">
                            <p class="sm:hidden text-[14px] font-medium mr-2">{{ 10 }}</span></p>
                            <x-mine.icon name="clock" variant="micro" class="sm:hidden " />
                            <x-mine.icon name="clock" variant="micro" class="hidden sm:inline " />
                            <p class="hidden sm:inline text-[14px] font-medium ml-2">{{ 10 }} <span class="hidden sm:inline">days left</span></p>
                        </div>
                    </div>
                </div>
                <div class="py-4 opacity-60">
                    <x-mine.separator />
                </div>
                <div class="flex justify-between items-center gap-6">
                    <div class="flex flex-col justify-between flex-2">
                        <h2 class="text-sm font-medium mine-text-primary mb-2">Progress</h2>
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-xl font-medium mine-text-link">{{ 40 }}%</h2>
                            <p class="sm:hidden text-[14px] font-medium mine-text-secondary">
                                <span class="mine-text-link text-md font-bold">{{ 4 }}</span> of <span class="mine-text-primary text-md font-bold">{{ 10 }}</span> completed
                            </p>
                        </div>
                        <x-mine.progress total="100" progress="40" class="h-3" />
                    </div>
                    <div class="hidden sm:flex flex-col items-end flex-1 gap-2">
                        <div class="avatar rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                            <x-mine.icon name="check" variant="micro" />
                            <p class="text-[14px] font-medium ml-2">{{ 4 }} completed</p>
                        </div>
                        <div class="avatar-secondary rounded-lg h-8 flex justify-center items-center pr-2 pl-1.5 min-w-35">
                            <x-mine.icon name="x-mark" variant="micro" />
                            <p class="text-[14px] font-medium ml-2">{{ 6 }} remaining</p>
                        </div>
                    </div>
                </div>
                <div class="py-4 opacity-60">
                    <x-mine.separator />
                </div>
                <div class="flex gap-4 justify-between items-center">
                    <x-mine.button class="mine-btn-outline-primary">
                        <div class="flex justify-center items-center gap-1 text-sm font-md">
                            <x-mine.icon name="plus" variant="micro" />
                            <p>Add Task</p>
                        </div>
                    </x-mine.button>
                    <x-mine.button class="mine-btn-primary">
                        <div class="flex justify-center items-center gap-1 text-sm font-medium">
                            <x-mine.icon name="check" variant="micro" />
                            <p class="hidden sm:block">Mark as Completed</p>
                            <p class="sm:hidden">Complete</p>
                        </div>
                    </x-mine.button>
                </div>
            </div>
        @empty
            @if($this->search)
                <div class="col-span-full text-center py-12">
                    <x-mine.icon name="magnifying-glass" class="size-12 mx-auto mb-3 mine-text-secondary" />
                    <p class="mine-text-secondary text-sm font-medium">No plans found.</p>
                </div>
            @else
                <div class="col-span-full text-center py-12">
                    <x-mine.icon name="folder-open" class="size-12 mx-auto mb-3 mine-text-secondary" />
                    <p class="mine-text-secondary text-sm font-medium">No plans yet. Create one above.</p>
                </div>
            @endif
        @endforelse
    </div>

    <x-mine.modal id="add-plan-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Add New Plan</h2>
                <button @click="close();"
                    class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            <form class="flex flex-col gap-4" wire:submit="">
                <div>
                    <x-mine.input label="Plan Name" wire:model="" placeholder="Enter plan name"/>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <x-mine.textarea label="Description" wire:model="" placeholder="Add description" />
                </div>
                <div>
                    <x-mine.datepicker mode="range" position="top-end" wire:model="" label="Range"/>
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close();" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="" class="mine-btn-primary">Add</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>








{{--

<div class="min-h-full bg-gradient-to-r from-slate-300 to-slate-50">
    <div class="flex flex-col md:flex-row p-4 gap-4">
        <div class="flex flex-col bg-gradient-to-r from-slate-50 to-slate-100 w-full flex-2 rounded-lg p-5">
            <x-ui.heading level="h2" size="md">Add new plan</x-ui.heading>
            <x-ui.separator class="my-4"></x-ui.separator>
            <form wire:submit="addPlan" class="flex flex-col gap-2">
                <x-ui.field class="flex-3">
                    <x-ui.label>Plan Name</x-ui.label>
                    <x-ui.input wire:model="plan_name" type="text" placeholder="Plan Name" leftIcon="" />
                    <x-ui.error name="plan_name" />
                </x-ui.field>
                <x-ui.field class="flex-3">
                    <x-ui.label>Description</x-ui.label>
                    <x-ui.textarea wire:model="description" placeholder="Description" leftIcon="" resize="none" />
                    <x-ui.error name="description" />
                </x-ui.field>
                <x-ui.field>
                    <x-ui.label>Plan Date</x-ui.label>
                    <x-ui.date-picker class="w-full" mode="range" wire:model="range" />
                    <x-ui.error name="range.end" />
                </x-ui.field>
                <x-ui.error name="plan_form" />
                <x-ui.button type="submit" class="w-full rounded-lg bg-slate-700 mt-4">
                    Add
                </x-ui.button>
            </form>
        </div>
        <div class="flex flex-col bg-gradient-to-r from-slate-50 to-slate-100 w-full flex-2 rounded-lg p-5">
            <x-ui.heading level="h2" size="md">Your Plans</x-ui.heading>
            <x-ui.separator class="my-4"></x-ui.separator>
            @forelse ($this->plans as $plan)
            <div class="flex justify-between gap-20 bg-slate-200 shadow-lg rounded-lg py-2 px-4 mb-4">
                <div class="flex flex-col justify-between gap-2">
                    <div>
                        <x-ui.text class="font-medium text-lg mb-2">{{ $plan->name }}</x-ui.text>
                        <x-ui.text class="text-black/60!">{{ $plan->description ?? 'No description' }}</x-ui.text>
                    </div>
                    <x-ui.text class="text-slate-700">{{ $plan->start_date->format('Y-m-d') }}
                        {{ $plan->finish_date->format('Y-m-d') }}</x-ui.text>
                </div>
                <div class="flex flex-col gap-2">
                    <x-ui.text class="text-black/60! text-right pr-2">{{ $plan->tasks_count ?: 'No Tasks' }}</x-ui.text>
                    <div class="flex flex-col gap-2">
                        <x-ui.button size="sm" wire:click="deletePlan({{ $plan->id }})"
                            class="w-20 rounded-lg bg-red-700">Delete</x-ui.button>
                        <x-ui.button size="sm"
                            x-on:click="$dispatch('open-modal', { id: 'edit-plan-modal' }); $wire.startEditing({{ $plan->id }})"
                            class="w-20 rounded-lg bg-slate-700">Edit</x-ui.button>
                        <x-ui.popover>
                            <x-ui.popover.trigger>
                                <x-ui.button class="w-full rounded-lg bg-gradient-to-r from-slate-800 to-slate-600">View
                                    Tasks</x-ui.button>
                            </x-ui.popover.trigger>
                            <x-ui.popover.overlay class="w-lg">
                                <div class="p-2 grid grid-cols-4 gap-4">
                                    @forelse ($plan->tasks as $task)
                                    <div wire:key="{{ $task->id }}"
                                        class="rounded-lg flex gap-2 justify-between bg-slate-200 w-full p-2 shadow-md">
                                        <x-ui.text>{{ $task->title }}</x-ui.text>
                                        <x-ui.icon name="{{ $task->done ? 'check' : 'backward' }}"></x-ui.icon>
                                    </div>
                                    @empty
                                    <div class="col-span-full text-center py-4">
                                        <x-ui.text class="text-black/50">No tasks assigned to this plan.</x-ui.text>
                                    </div>
                                    @endforelse
                                    @if($plan->tasks_count > 0)
                                    <div class="col-span-full space-y-2">
                                        @php
                                        $doneCount = $plan->tasks->where('done', true)->count();
                                        $progress = round(($doneCount / $plan->tasks_count) * 100);
                                        @endphp
                                        <x-ui.text size="sm" class="font-medium">{{ $doneCount }}/{{ $plan->tasks_count
                                            }} ({{ $progress }}%) Progress</x-ui.text>
                                        <x-ui.progress value="{{ $progress }}" />
                                    </div>
                                    @endif
                                </div>
                            </x-ui.popover.overlay>
                        </x-ui.popover>
                    </div>
                </div>
            </div>
            @empty
            <x-ui.text class="text-black/50 text-center py-8">No plans yet. Create one above.</x-ui.text>
            @endforelse
            <x-ui.progress value="65" size="lg" />
        </div>
    </div>

    <x-ui.modal bare backdrop="dark" position="center" width="3xl" id="edit-plan-modal" :close-by-clicking-away="false">
        <div class="flex flex-col m-5">
            <div class="flex justify-between items-center px-1">
                <x-ui.heading level="h2" size="md">Edit Plan</x-ui.heading>
                <x-ui.icon x-on:click="$data.close(); $wire.cancelEditing()" name="x-mark"
                    class="size-7 opacity-80 hover:cursor-pointer"></x-ui.icon>
            </div>
            <x-ui.separator class="mt-4 mb-8" />
            <form class="p-2" wire:submit="updatePlan">
                <div class="mb-4">
                    <x-ui.field>
                        <x-ui.label>Plan Name</x-ui.label>
                        <x-ui.input wire:model="editName" type="text" placeholder="Plan Name" leftIcon="" />
                        <x-ui.error name="editName" />
                    </x-ui.field>
                </div>
                <div class="mb-4">
                    <x-ui.field>
                        <x-ui.label>Description</x-ui.label>
                        <x-ui.textarea wire:model="editDescription" placeholder="Description" leftIcon=""
                            resize="none" />
                        <x-ui.error name="editDescription" />
                    </x-ui.field>
                </div>
                <div class="mb-12">
                    <x-ui.field>
                        <x-ui.label>Plan Date</x-ui.label>
                        <x-ui.date-picker class="w-full" mode="range" wire:model="editRange" />
                        <x-ui.error name="editRange.end" />
                    </x-ui.field>
                    <x-ui.error name="edit_form" />
                </div>
                <div class="flex justify-between items-center gap-5">
                    <x-ui.button type="button" x-on:click="$data.close(); $wire.cancelEditing()"
                        class="w-full rounded-lg bg-gradient-to-r from-red-800 to-red-600">Cancel</x-ui.button>
                    <x-ui.button type="submit" wire:target="updatePlan"
                        class="w-full rounded-lg bg-gradient-to-r from-slate-800 to-slate-600">Save</x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>
</div> --}}
