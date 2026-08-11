<div class="min-h-full bg-gradient-to-r from-slate-300 to-slate-50">
    <div class="flex flex-col p-4 gap-4">
        <x-ui.text class="text-2xl font-semibold">
            Upcoming Tasks
        </x-ui.text>

        @forelse ($this->upcomingTasks as $task)
            <div wire:key="{{ $task->id }}"
                class="flex justify-between bg-white rounded-xl p-4 shadow-sm">
                <div class="flex flex-col gap-1">
                    <x-ui.text class="font-medium text-lg">
                        {{ $task->title }}
                    </x-ui.text>
                    <div class="flex gap-2 items-center text-sm text-slate-500">
                        <span>{{ $task->task_date->format('Y-m-d') }}</span>
                        <span>&middot;</span>
                        <span>
                            @php
                                $daysUntil = now()->startOfDay()->diffInDays($task->task_date, false);
                            @endphp
                            @if ($daysUntil === 0)
                                Due today
                            @elseif ($daysUntil === 1)
                                Due tomorrow
                            @else
                                Due in {{ $daysUntil }} days
                            @endif
                        </span>
                    </div>
                    <div class="flex gap-3 items-center text-sm mt-1">
                        <x-ui.text class="text-slate-600">{{ $task->category->name }}</x-ui.text>
                        @if ($task->plan)
                            <x-ui.text class="text-slate-400">&middot;</x-ui.text>
                            <x-ui.text class="text-slate-600">{{ $task->plan->name }}</x-ui.text>
                        @endif
                    </div>
                </div>
                <div class="flex items-center">
                    @if ($task->done)
                        <x-ui.text class="text-green-600 font-medium">Done</x-ui.text>
                    @else
                        <x-ui.text class="text-amber-600 font-medium">Not Done</x-ui.text>
                    @endif
                </div>
            </div>
        @empty
            <x-ui.text class="text-black/50 text-center py-8">
                No upcoming tasks.
            </x-ui.text>
        @endforelse

        <x-ui.link variant="soft" href="{{ route('tasks') }}" wire:navigate
            class="text-slate-600 font-medium text-sm mt-2">
            View All Tasks &rarr;
        </x-ui.link>
    </div>
</div>
