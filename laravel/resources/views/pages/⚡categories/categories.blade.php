<div class="flex flex-1 flex-col px-4 pt-4 pb-6 sm:px-8 md:px-16">
    <x-mine.animate class="mb-6">
        <h1 class="mine-text-primary text-base font-bold">{{ __('My categories') }}</h1>
    </x-mine.animate>
    @island(name: 'category-content', always: true)
        <x-mine.animate delay="50" class="mb-6 flex items-center justify-between gap-2 sm:gap-4">
            <div class="w-full">
                <x-mine.input
                    wire:model.live.debounce.200ms="search"
                    placeholder="{{ __('Search categories...') }}"
                    leftIcon="Magnifier"
                />
            </div>
            <x-mine.modal.trigger id="add-category-form">
                <x-mine.button
                    type="button"
                    @class([
                        'sm:pr-4!' => app()->isLocale('en'),
                        'sm:pl-4!' => app()->isLocale('fa'),
                        'mine-btn-primary px-3.5! sm:px-3!',
                    ])
                >
                    <div class="flex items-center justify-center gap-2">
                        <x-mine.icon
                            name="Plus"
                            @class([
                                'mb-1' => app()->isLocale('en'),
                            ])
                            size="16"
                            weight="filled"
                        />
                        <p class="hidden text-sm font-medium sm:inline">
                            <span class="hidden sm:inline md:hidden">{{ __('New') }}</span
                            ><span class="hidden md:inline">{{ __('New category') }}</span>
                        </p>
                    </div>
                </x-mine.button>
            </x-mine.modal.trigger>
            <x-mine.dropdown>
                <x-mine.dropdown.trigger as="div">
                    <div @class([
                        'sm:pl-3!' => app()->isLocale('en'),
                        'sm:pr-3!' => app()->isLocale('fa'),
                        'w-full mine-btn-primary flex items-center h-11 px-5 sm:px-4 rounded-xl cursor-pointer select-none',
                    ])>
                        <x-mine.icon
                            name="ArrowUp"
                            @class([
                                '-mr-1 -ml-2.5 sm:ml-0' => app()->isLocale('en'),
                                '-ml-1 -mr-2.5 sm:mr-0' => app()->isLocale('fa'),
                                'inline',
                            ])
                            size="16"
                            weight="filled"
                        />
                        <x-mine.icon
                            name="ArrowDown"
                            @class([
                                '-ml-1 -mr-2.5 sm:mr-0' => app()->isLocale('en'),
                                '-mr-1 -ml-2.5 sm:ml-0' => app()->isLocale('fa'),
                                'inline',
                            ])
                            size="16"
                            weight="filled"
                        />
                        <p @class([
                            'sm:pl-1 pt-1' => app()->isLocale('en'),
                            'sm:pr-1' => app()->isLocale('fa'),
                            'text-sm font-medium hidden sm:inline',
                        ])>
                            <span class="hidden sm:inline md:hidden">{{ __('Sort') }}</span
                            ><span class="hidden md:inline">{{ __('Sort by') }}</span>
                        </p>
                    </div>
                </x-mine.dropdown.trigger>
                <x-mine.dropdown.content class="mt-1!" placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                    @foreach ([
                        'latest' => __('Date created'),
                        'name' => __('Category name'),
                        'tasks' => __('Number of tasks'),
                    ] as $value => $label)
                        <x-mine.dropdown.item>
                            <div
                                class="w-full rounded-lg py-2 px-4 flex items-center hover:cursor-pointer {{ $sort === $value ? 'bg-(--mine-dropdown-item-bg-hover)' : '' }}"
                                @click="$wire.$island('category-content').$set('sort', '{{ $value }}')"
                            >
                                <p class="text-sm font-medium {{ app()->isLocale('en') ? 'pt-1' : '' }} {{ $sort === $value ? 'mine-text-link' : 'mine-text-secondary' }}">
                                    {{ $label }}
                                </p>
                            </div>
                        </x-mine.dropdown.item>
                    @endforeach
                </x-mine.dropdown.content>
            </x-mine.dropdown>
        </x-mine.animate>
        <div class="relative">
            <div wire:loading.delay.short class="absolute inset-0 z-10">
                <div class="flex h-full w-full items-center justify-center">
                    <svg
                        class="mine-text-secondary size-8 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        role="status"
                        aria-label="Loading"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                </div>
            </div>
            <div wire:loading.delay.short.class="opacity-40" class="transition-opacity">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    @forelse ($this->categories as $category)
                        <x-mine.animate
                            wire:key="category-{{ $category->id }}"
                            stagger="60"
                            data-anim-index="{{ $loop->index }}"
                            @class([
                                'border-l-6 border-l-(--mine-btn-primary-bg) hover:border-l-(--mine-btn-primary-bg-hover)' => app()->isLocale('en'),
                                'border-r-6 border-r-(--mine-btn-primary-bg) hover:border-r-(--mine-btn-primary-bg-hover)' => app()->isLocale('fa'),
                                'mine-card w-full flex flex-col justify-between px-4 pt-4 pb-4',
                            ])
                        >
                            <div class="mb-3 flex items-start justify-between">
                                <div class="flex min-w-0 gap-4">
                                    <div class="mine-badge-primary flex size-16 shrink-0 items-center justify-center rounded-xl">
                                        <x-mine.icon name="Folder" weight="filled" size="32" />
                                    </div>
                                    <div class="flex min-w-0 flex-col justify-between py-1">
                                        <h2 @class([
                                            'pt-1' => app()->isLocale('en'),
                                            'mine-text-primary font-semibold text-[15px] truncate',
                                        ])>
                                            {{ $category->name }}
                                        </h2>
                                        <p class="mine-text-secondary text-[13px] font-medium">
                                            {{ $category->tasks_count ? (app()->isLocale('en') ? $category->tasks_count : App\Support\PersianNumber::convert($category->tasks_count)) : __('No') }} {{ app()->isLocale('fa') ? 'تسک' : Str::plural('Task', $category->tasks_count) }}
                                        </p>
                                    </div>
                                </div>
                                <x-mine.dropdown group="category-actions">
                                    <x-mine.dropdown.trigger>
                                        <div class="mine-btn-icon rounded-xl p-2">
                                            <x-mine.icon name="MoreH" size="18" weight="filled" />
                                        </div>
                                    </x-mine.dropdown.trigger>
                                    <x-mine.dropdown.content placement="bottom-{{ app()->isLocale('en') ? 'end' : 'start' }}">
                                        <x-mine.dropdown.item>
                                            <div
                                                class="mine-text-link flex w-full items-center gap-2 px-4 py-2 hover:cursor-pointer"
                                                @click.stop="$wire.set('editing_id', {{ $category->id }}, false);
                                            $wire.set('edit_name', @js($category->name), false);
                                            $dispatch('open-modal', { id: 'edit-category-form' })"
                                            >
                                                <p @class([
                                                    'pt-0.5' => app()->isLocale('en'),
                                                    'text-sm font-medium',
                                                ])>
                                                    {{ __('Edit') }}
                                                </p>
                                            </div>
                                        </x-mine.dropdown.item>
                                        <x-mine.dropdown.item destructive>
                                            <div
                                                class="mine-text-error flex w-full items-center gap-2 px-4 py-2 hover:cursor-pointer"
                                                @click.stop="$wire.set('deleting_id', {{ $category->id }}, false);
                                            $dispatch('open-modal', { id: 'delete-category-confirmation' })"
                                            >
                                                <p @class([
                                                    'pt-0.5' => app()->isLocale('en'),
                                                    'text-sm font-medium',
                                                ])>
                                                    {{ __('Delete') }}
                                                </p>
                                            </div>
                                        </x-mine.dropdown.item>
                                    </x-mine.dropdown.content>
                                </x-mine.dropdown>
                            </div>
                            <div class="mb-4 opacity-40">
                                <x-mine.separator />
                            </div>
                            <div class="flex items-center justify-center">
                                <a
                                    href="{{ route('tasks', ['category_filter' => [$category->id]]) }}"
                                    wire:navigate.hover
                                    class="mine-text-link flex w-full items-center justify-between gap-2 px-2"
                                >
                                    <p @class([
                                        'pt-1' => app()->isLocale('en'),
                                        'text-[13px] font-semibold',
                                    ])>
                                        {{ __('View tasks') }}
                                    </p>
                                    <x-mine.icon
                                        name="Arrow{{ app()->isLocale('en') ? 'Right' : 'Left' }}"
                                        weight="filled"
                                        size="18"
                                        class="mt-0.5"
                                    />
                                </a>
                            </div>
                        </x-mine.animate>
                    @empty
                        @if ($this->search)
                            <x-mine.animate class="col-span-full py-12 text-center">
                                <x-mine.icon
                                    name="Magnifier"
                                    size="40"
                                    weight="filled"
                                    class="mine-text-secondary mx-auto mb-3"
                                />
                                <p class="mine-text-secondary text-sm font-medium">{{ __('No categories found') }}</p>
                            </x-mine.animate>
                        @else
                            <x-mine.animate class="col-span-full py-12 text-center">
                                <x-mine.icon
                                    name="Folder"
                                    size="40"
                                    weight="filled"
                                    class="mine-text-secondary mx-auto mb-3"
                                />
                                <p class="mine-text-secondary text-sm font-medium">{{ __('No categories yet') }}</p>
                            </x-mine.animate>
                        @endif
                    @endforelse
                </div>
            </div>
        </div>
        <x-mine.animate class="mt-6 w-full">
            {{ $this->categories->links(data: ['scrollTo' => false]) }}
        </x-mine.animate>
    @endisland

    <x-mine.modal id="add-category-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Add new category') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelAdd();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if ($add_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Duplicate category!') }}"
                    >
                        {{ __('You have a category with this name.') }}</x-mine.alert>
                </div>
            @endif

            @if ($add_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert
                        variant="warning"
                        title="{{ __('Too many add-category attempts!') }}"
                    >
                        {{ __('Try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="addCategory">
                <div>
                    <x-mine.input
                        label="{{ __('Category name') }}"
                        wire:model="add_category"
                        placeholder="{{ __('Your category name') }}"
                        leftIcon="Folder"
                    />
                </div>
                <div class="mt-4 flex justify-end gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelAdd();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="addCategory" class="mine-btn-primary">
                        <p class="text-sm">{{ __('Create') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-category-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Edit category') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelEdit();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if ($edit_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Duplicate category!') }}"
                    >
                        {{ __('You have a category with this name.') }}</x-mine.alert>
                </div>
            @endif

            @if ($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert
                        variant="warning"
                        title="{{ __('Too many edit-category attempts!') }}"
                    >
                        {{ __('Try again in a minute.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editCategory">
                <div>
                    <x-mine.input
                        label="{{ __('Category name') }}"
                        wire:model="edit_name"
                        placeholder="{{ __('Your category name') }}"
                        leftIcon="Folder"
                    />
                </div>
                <div class="mt-4 flex justify-end gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelEdit();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="editCategory" class="mine-btn-primary">
                        <p class="text-sm">{{ __('Edit') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal
        id="delete-category-confirmation"
        :close-by-clicking-away="false"
        :close-by-escaping="false"
        width="lg"
    >
        <div class="px-6 py-6 sm:px-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="mine-text-primary text-base font-semibold">{{ __('Delete category') }}</h2>
                <button
                    type="button"
                    @click="
                        close();
                        $wire.cancelDelete();
                    "
                    class="mine-btn-icon rounded-xl p-2"
                >
                    <x-mine.icon name="Xmark" weight="filled" size="16" />
                </button>
            </div>

            @if ($delete_error === 'has_tasks')
                <div class="mb-4">
                    <x-mine.alert
                        variant="danger"
                        title="{{ __('Cannot delete category!') }}"
                    >
                        {{ __('This category has tasks, reassign or delete them first.') }}</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert
                        variant="warning"
                        title="{{ __('Are you sure?') }}"
                    >
                        {{ __('This action cannot be undone.') }}</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="deleteCategory">
                <div class="mt-4 flex justify-end gap-4">
                    <x-mine.button
                        type="button"
                        @click="
                            close();
                            $wire.cancelDelete();
                        "
                        class="mine-btn-ghost"
                    >
                        <p class="text-sm">{{ __('Cancel') }}</p>
                    </x-mine.button>
                    <x-mine.button wire:target="deleteCategory" class="mine-btn-danger">
                        <p class="text-sm">{{ __('Delete') }}</p>
                    </x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>
