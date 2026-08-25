<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6">
        <h1 class="font-bold text-base mine-text-primary">{{ __('My categories') }}</h1>
    </div>
    @island(name: 'category-content', always: true)
    <div class="flex items-center justify-between gap-2 sm:gap-4 mb-6">
        <div class="w-full">
            <x-mine.input wire:model.live.debounce.200ms="search" placeholder="{{ __('Search categories...') }}"
                leftIcon="magnifying-glass" />
        </div>
        <x-mine.modal.trigger id="add-category-form">
            <x-mine.button type="button" @class([
                "sm:pr-4!" => app()->isLocale('en'),
                "sm:pl-4!" => app()->isLocale('fa'),
                "mine-btn-primary px-3.5! sm:px-3!"
            ])>
                <div class="flex justify-center items-center gap-2">
                    <x-mine.icon name="plus" @class([
                        "mb-1" => app()->isLocale('en'),
                        "size-4"
                    ]) variant="micro" />
                    <p class="text-sm font-medium hidden sm:inline"><span class="hidden sm:inline md:hidden">{{ __('New') }}</span><span class="hidden md:inline">{{ __('New category') }}</span></p>
                </div>
            </x-mine.button>
        </x-mine.modal.trigger>
        <x-mine.dropdown>
            <x-mine.dropdown.trigger as="div">
                <div @class([
                    "sm:pl-3!" => app()->isLocale('en'),
                    "sm:pr-3!" => app()->isLocale('fa'),
                    "w-full mine-btn-primary flex items-center h-11 px-5 sm:px-4 rounded-xl cursor-pointer select-none"
                ])>
                    <x-mine.icon name="arrow-long-up" @class([
                        "-mr-1 -ml-2.5 sm:ml-0" => app()->isLocale('en'),
                        "-ml-1 -mr-2.5 sm:mr-0" => app()->isLocale('fa'),
                        "inline size-4"
                    ]) variant="micro" />
                    <x-mine.icon name="arrow-long-down" @class([
                        "-ml-1 -mr-2.5 sm:mr-0" => app()->isLocale('en'),
                        "-mr-1 -ml-2.5 sm:ml-0" => app()->isLocale('fa'),
                        "inline size-4"
                    ]) variant="micro" />
                    <p @class([
                        "sm:pl-1 pt-1" => app()->isLocale('en'),
                        "sm:pr-1" => app()->isLocale('fa'),
                        "text-sm font-medium hidden sm:inline"
                    ])><span class="hidden sm:inline md:hidden">{{ __('Sort') }}</span><span class="hidden md:inline">{{ __('Sort by') }}</span></p>
                </div>
            </x-mine.dropdown.trigger>
            <x-mine.dropdown.content class="mt-1!" placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                @foreach ([
                    'latest' => __('Date created'),
                    'name' => __('Category name'),
                    'tasks' => __('Number of tasks'),
                ] as $value => $label)
                    <x-mine.dropdown.item>
                        <div class="w-full rounded-lg py-2 px-4 flex items-center hover:cursor-pointer {{ $sort === $value ? 'bg-(--mine-dropdown-item-bg-hover)' : '' }}"
                            @click="$wire.$island('category-content').$set('sort', '{{ $value }}')">
                            <p class="text-sm font-medium {{ app()->isLocale('en') ? 'pt-1' : '' }} {{ $sort === $value ? 'mine-text-link' : 'mine-text-secondary' }}">{{ $label }}</p>
                        </div>
                    </x-mine.dropdown.item>
                @endforeach
            </x-mine.dropdown.content>
        </x-mine.dropdown>
    </div>
    <div class="relative">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @forelse ($this->categories as $category)
                <div wire:key="category-{{ $category->id }}" @class([
                    "border-l-6 border-l-(--mine-btn-primary-bg) hover:border-l-(--mine-btn-primary-bg-hover)" => app()->isLocale('en'),
                    "border-r-6 border-r-(--mine-btn-primary-bg) hover:border-r-(--mine-btn-primary-bg-hover)" => app()->isLocale('fa'),
                    "mine-card-interactive w-full flex flex-col justify-between px-4 pt-4 pb-4"
                ])>
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex gap-4 min-w-0">
                            <div class="rounded-xl size-16 shrink-0 mine-badge-primary flex justify-center items-center">
                                <x-mine.icon name="folder" class="size-8" variant="mini" />
                            </div>
                            <div class="flex flex-col justify-between py-1 min-w-0">
                                <h2 @class([
                                    "pt-1" => app()->isLocale('en'),
                                    "mine-text-primary font-semibold text-[15px] truncate"
                                ])>{{ $category->name }}</h2>
                                <p class="mine-text-secondary text-[13px] font-medium">{{ $category->tasks_count ? (app()->isLocale('en') ? $category->tasks_count : App\Support\PersianNumber::show($category->tasks_count)) : __('No') }} {{ app()->isLocale('fa') ? 'تسک' : Str::plural('Task', $category->tasks_count) }}</p>
                            </div>
                        </div>
                        <x-mine.dropdown group="category-actions">
                            <x-mine.dropdown.trigger>
                                <div class="mine-btn-icon p-2 rounded-xl">
                                    <x-mine.icon name="ellipsis-horizontal" class="size-5" variant="mini" />
                                </div>
                            </x-mine.dropdown.trigger>
                            <x-mine.dropdown.content placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                                <x-mine.dropdown.item>
                                    <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-link hover:cursor-pointer"
                                        @click.stop="$wire.set('editing_id', {{ $category->id }}, false);
                                            $wire.set('edit_name', @js($category->name), false);
                                            $dispatch('open-modal', { id: 'edit-category-form' })">
                                        <p @class([
                                            "pt-0.5" => app()->isLocale('en'),
                                            "text-sm font-medium"
                                        ])>{{ __('Edit') }}</p>
                                    </div>
                                </x-mine.dropdown.item>
                                <x-mine.dropdown.item destructive>
                                    <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-error hover:cursor-pointer"
                                        @click.stop="$wire.set('deleting_id', {{ $category->id }}, false);
                                            $dispatch('open-modal', { id: 'delete-category-confirmation' })">
                                        <p @class([
                                            "pt-0.5" => app()->isLocale('en'),
                                            "text-sm font-medium"
                                        ])>{{ __('Delete') }}</p>
                                    </div>
                                </x-mine.dropdown.item>
                            </x-mine.dropdown.content>
                        </x-mine.dropdown>
                    </div>
                    <div class="opacity-40 mb-4">
                        <x-mine.separator />
                    </div>
                    <div class="flex items-center justify-center">
                        <a href="{{ route('tasks', ['category_filter' => [$category->id]]) }}" wire:navigate.hover class="w-full flex items-center justify-between px-2 gap-2 mine-text-link">
                            <p @class([
                                "pt-1" => app()->isLocale('en'),
                                "text-[13px] font-semibold"
                            ])>{{ __('View tasks') }}</p>
                            <x-mine.icon name="arrow-long-{{ app()->isLocale('en') ? 'right' : 'left' }}" variant="micro" class="size-4 mt-0.5" />
                        </a>
                    </div>
                </div>
                @empty
                    @if($this->search)
                        <div class="col-span-full text-center py-12">
                            <x-mine.icon name="magnifying-glass" class="size-10 mx-auto mb-3 mine-text-secondary" variant="mini" />
                            <p class="mine-text-secondary text-sm font-medium">{{ __('No categories found') }}</p>
                        </div>
                    @else
                        <div class="col-span-full text-center py-12">
                            <x-mine.icon name="folder" class="size-10 mx-auto mb-3 mine-text-secondary" variant="mini" />
                            <p class="mine-text-secondary text-sm font-medium">{{ __('No categories yet') }}</p>
                        </div>
                    @endif
                @endforelse
            </div>
        </div>
    </div>
    <div class="mt-6 w-full">
        {{ $this->categories->links(data: ['scrollTo' => false]) }}
    </div>
    @endisland

    <x-mine.modal id="add-category-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Add new category') }}</h2>
                <button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($add_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Duplicate category!') }}">{{ __('A category with this name already exists.') }}</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many create-category attempts!') }}">{{ __('Please try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="addCategory">
                <div>
                    <x-mine.input label="{{ __('Category name') }}" wire:model="add_category" placeholder="{{ __('Enter category name') }}" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="addCategory" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Create') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-category-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Edit category') }}</h2>
                <button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($edit_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Duplicate category!') }}">{{ __('A category with this name already exists.') }}</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Too many edit-category attempts!') }}">{{ __('Please try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editCategory">
                <div>
                    <x-mine.input label="{{ __('Category name') }}" wire:model="edit_name" placeholder="{{ __('Enter category name') }}" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="editCategory" class="mine-btn-primary">
                        <p class="text-sm">
                            {{ __('Edit') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-category-confirmation" :close-by-clicking-away="false" :close-by-escaping="false"
        width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-base font-semibold mine-text-primary">{{ __('Delete category') }}</h2>
                <button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-icon p-2 rounded-xl">
                    <x-mine.icon name="x-mark" variant="micro" />
                </button>
            </div>

            @if($delete_error === 'has_tasks')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="{{ __('Cannot delete category!') }}">{{ __('This category has tasks, reassign or delete them first.') }}</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="{{ __('Are you sure?') }}">{{ __('This action cannot be undone.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="deleteCategory">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-ghost">
                        <p class="text-sm">
                            {{ __('Cancel') }}
                        </p>
                    </x-mine.button>
                    <x-mine.button wire:target="deleteCategory" class="mine-btn-danger">
                        <p class="text-sm">
                            {{ __('Delete') }}
                        </p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>
