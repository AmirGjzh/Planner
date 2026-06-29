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
                        </div>
                    </div>
                </div>
            @empty
                <x-ui.text class="text-black/50 text-center py-8">No plans yet. Create one above.</x-ui.text>
            @endforelse
        </div>
    </div>

    <x-ui.modal bare backdrop="dark" position="center" width="3xl" id="edit-plan-modal"
        :close-by-clicking-away="false">
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
                        <x-ui.textarea wire:model="editDescription" placeholder="Description" leftIcon="" resize="none" />
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
</div>
