<div class="min-h-full bg-gradient-to-r from-slate-300 to-slate-50">
    <div class="flex flex-col p-4 gap-4">
        <x-ui.text class="text-2xl font-semibold">
            Reports
        </x-ui.text>

        <div class="flex gap-4 items-center">
            <x-ui.date-picker mode="range" wire:model="date_filter"></x-ui.date-picker>
            <x-ui.button wire:click="applyDateFilter" class="rounded-xl bg-slate-700">
                Filter
            </x-ui.button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <x-ui.text class="text-sm text-slate-500">Tasks Created</x-ui.text>
                <x-ui.text class="text-3xl font-bold text-slate-800">
                    {{ $this->stats['tasks_created'] }}
                </x-ui.text>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm">
                <x-ui.text class="text-sm text-slate-500">Tasks Completed</x-ui.text>
                <x-ui.text class="text-3xl font-bold text-green-600">
                    {{ $this->stats['tasks_completed'] }}
                </x-ui.text>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm">
                <x-ui.text class="text-sm text-slate-500">Completion Rate</x-ui.text>
                <x-ui.text class="text-3xl font-bold text-blue-600">
                    {{ $this->stats['completion_rate'] }}%
                </x-ui.text>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm">
                <x-ui.text class="text-sm text-slate-500">Overdue Tasks</x-ui.text>
                <x-ui.text class="text-3xl font-bold text-red-600">
                    {{ $this->stats['overdue_count'] }}
                </x-ui.text>
            </div>
        </div>
    </div>
</div>
