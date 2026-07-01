<div class="min-h-full bg-gradient-to-r from-slate-300 to-slate-50">
    <div class="flex flex-col md:flex-row p-4 gap-4">
        <div class="flex flex-col bg-gradient-to-r from-slate-50 to-slate-100 w-full flex-2 rounded-lg p-5">
            <x-ui.heading level="h2" size="md">Add new task</x-ui.heading>
            <x-ui.separator class="my-4"></x-ui.separator>
            <form wire:submit="addTask" class="flex flex-col gap-2">
                <x-ui.field class="flex-3">
                    <x-ui.label>Task Title</x-ui.label>
                    <x-ui.input wire:model="task_title" type="text" placeholder="Title" leftIcon="" />
                    <x-ui.error name="task_title" />
                </x-ui.field>
                <x-ui.field class="flex-3">
                    <x-ui.label>Description</x-ui.label>
                    <x-ui.textarea wire:model="task_description" placeholder="Description" leftIcon="" resize="none" />
                    <x-ui.error name="task_description" />
                </x-ui.field>
                <x-ui.field>
                    <x-ui.label>Task Date</x-ui.label>
                    <x-ui.date-picker class="w-full" mode="single" wire:model="task_date" />
                    <x-ui.error name="task_date" />
                </x-ui.field>
                <x-ui.field>
                    <x-ui.label>Task Estimated Minutes</x-ui.label>
                    <x-ui.input wire:model="task_estimated_minutes" type="number" leftIcon="" />
                    <x-ui.error name="task_estimated_minutes" />
                </x-ui.field>
                <x-ui.field>
                    <x-ui.label>Days before alarm</x-ui.label>
                    <x-ui.input wire:model="task_alarm_days" type="number" leftIcon="" />
                    <x-ui.error name="task_alarm_days" />
                </x-ui.field>
                <x-ui.field>
                    <x-ui.label>Task Priority</x-ui.label>
                    <x-ui.select placeholder="Select Priority" wire:model="task_priority">
                        <x-ui.select.option value="low">
                            Low
                        </x-ui.select.option>
                        <x-ui.select.option value="medium">
                            Medium
                        </x-ui.select.option>
                        <x-ui.select.option value="high">
                            High
                        </x-ui.select.option>
                    </x-ui.select>
                    <x-ui.error name="task_priority" />
                </x-ui.field>
                <x-ui.field>
                    <x-ui.label>Task Category</x-ui.label>
                    <x-ui.select placeholder="Select Category" wire:model="task_category_id">
                        @foreach ($this->categories as $category)
                            <x-ui.select.option value="{{ $category->id }}">
                                {{ $category->name }}
                            </x-ui.select.option>
                        @endforeach
                    </x-ui.select>
                    <x-ui.error name="task_category_id" />
                </x-ui.field>
                <x-ui.field>
                    <x-ui.label>Task Plan</x-ui.label>
                    <x-ui.select placeholder="Select Plan" wire:model="task_plan_id">
                        @foreach ($this->plans as $plan)
                            <x-ui.select.option value="{{ $plan->id }}">
                                {{ $plan->name }}
                            </x-ui.select.option>
                        @endforeach
                    </x-ui.select>
                    <x-ui.error name="task_plan_id" />
                </x-ui.field>
                <x-ui.error name="task_form" />
                <x-ui.button type="submit" class="w-full rounded-lg bg-slate-700 mt-4">
                    Add
                </x-ui.button>
            </form>
        </div>
        <div class="flex flex-col bg-gradient-to-r from-slate-50 to-slate-100 w-full flex-2 rounded-lg p-5">
            <x-ui.heading level="h2" size="md">Your Tasks</x-ui.heading>
            <x-ui.separator class="my-4"></x-ui.separator>
            <x-ui.error name="toggle_task" class="mb-4"></x-ui.error>
            @forelse ($this->tasks as $task)
                <div class="flex justify-between gap-20 bg-slate-200 shadow-lg rounded-lg py-2 px-4 mb-4">
                    <div class="flex flex-col justify-between gap-2">
                        <div>
                            <x-ui.text class="font-medium text-lg mb-2">{{ $task->title }}</x-ui.text>
                            <x-ui.text class="text-black/60!">{{ $task->description ?? 'No description' }}</x-ui.text>
                        </div>
                        <div class="flex">
                            <x-ui.text class="text-slate-700">{{ $task->task_date->format('Y-m-d') }}</x-ui.text>
                            <x-ui.separator class="mx-2" vertical></x-ui.separator>
                            <x-ui.text class="text-slate-700">Takes {{ $task->estimated_minutes }} minutes</x-ui.text>
                        </div>
                    </div>
                    <div class="flex flex-col justify-between gap-4">
                        <div class="self-end">
                            <x-ui.switch size="md" wire:click="toggleTask({{ $task->id }})" label="Finished task" name="" :checked="$task->done" />
                        </div>
                        <div class="flex justify-end">
                            <x-ui.text class="text-black! text-right pr-2">{{ $task->plan->name ?? 'No plan'
                                    }}</x-ui.text>
                            <x-ui.text class="text-black! text-right pr-2">{{ $task->category->name
                                    }}</x-ui.text>
                            <x-ui.text class="text-black/60! text-right pr-2">{{ $task->priority
                                    }}</x-ui.text>
                        </div>
                        <div class="flex flex-col gap-2 items-end">
                            <x-ui.button size="sm" wire:click="deleteTask({{ $task->id }})"
                                class="w-20 rounded-lg bg-red-700">Delete</x-ui.button>
                            <x-ui.button size="sm"
                                x-on:click="$dispatch('open-modal', { id: 'edit-task-modal' }); $wire.startEditing({{ $task->id }})"
                                class="w-20 rounded-lg bg-slate-700">Edit</x-ui.button>
                        </div>
                    </div>
                </div>
            @empty
                <x-ui.text class="text-black/50 text-center py-8">No Tasks yet. Create one above.</x-ui.text>
            @endforelse
        </div>
    </div>

    <x-ui.modal bare backdrop="dark" position="center" width="3xl" id="edit-task-modal" :close-by-clicking-away="false">
        <div class="flex flex-col m-5">
            <div class="flex justify-between items-center px-1">
                <x-ui.heading level="h2" size="md">Edit Task</x-ui.heading>
                <x-ui.icon x-on:click="$data.close(); $wire.cancelEditing()" name="x-mark"
                    class="size-7 opacity-80 hover:cursor-pointer"></x-ui.icon>
            </div>
            <x-ui.separator class="mt-4 mb-8" />
            <form class="p-2" wire:submit="updateTask">
                <div class="mb-4">
                    <x-ui.field>
                        <x-ui.label>Task Title</x-ui.label>
                        <x-ui.input wire:model="editTitle" type="text" placeholder="Title" leftIcon="" />
                        <x-ui.error name="editTitle" />
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
                <div class="mb-4">
                    <x-ui.field>
                        <x-ui.label>Task Date</x-ui.label>
                        <x-ui.date-picker class="w-full" mode="single" wire:model="editDate" />
                        <x-ui.error name="editDate" />
                    </x-ui.field>
                </div>
                <div class="mb-4">
                    <x-ui.field>
                        <x-ui.label>Estimated Minutes</x-ui.label>
                        <x-ui.input wire:model="editEstimatedMinutes" type="number" leftIcon="" />
                        <x-ui.error name="editEstimatedMinutes" />
                    </x-ui.field>
                </div>
                <div class="mb-4">
                    <x-ui.field>
                        <x-ui.label>Days before alarm</x-ui.label>
                        <x-ui.input wire:model="editAlarmDays" type="number" leftIcon="" />
                        <x-ui.error name="editAlarmDays" />
                    </x-ui.field>
                </div>
                <div class="mb-4">
                    <x-ui.field>
                        <x-ui.label>Priority</x-ui.label>
                        <x-ui.select placeholder="Select Priority" wire:model="editPriority">
                            <x-ui.select.option value="low">Low</x-ui.select.option>
                            <x-ui.select.option value="medium">Medium</x-ui.select.option>
                            <x-ui.select.option value="high">High</x-ui.select.option>
                        </x-ui.select>
                        <x-ui.error name="editPriority" />
                    </x-ui.field>
                </div>
                <div class="mb-4">
                    <x-ui.field>
                        <x-ui.label>Category</x-ui.label>
                        <x-ui.select placeholder="Select Category" wire:model="editCategoryId">
                            @foreach ($this->categories as $category)
                                <x-ui.select.option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="editCategoryId" />
                    </x-ui.field>
                </div>
                <div class="mb-12">
                    <x-ui.field>
                        <x-ui.label>Plan</x-ui.label>
                        <x-ui.select placeholder="Select Plan" wire:model="editPlanId">
                            @foreach ($this->plans as $plan)
                                <x-ui.select.option value="{{ $plan->id }}">
                                    {{ $plan->name }}
                                </x-ui.select.option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.error name="editPlanId" />
                    </x-ui.field>
                    <x-ui.error name="edit_form" />
                </div>
                <div class="flex justify-between items-center gap-5">
                    <x-ui.button type="button" x-on:click="$data.close(); $wire.cancelEditing()"
                        class="w-full rounded-lg bg-gradient-to-r from-red-800 to-red-600">Cancel</x-ui.button>
                    <x-ui.button type="submit" wire:target="updateTask"
                        class="w-full rounded-lg bg-gradient-to-r from-slate-800 to-slate-600">Save</x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>
</div>
