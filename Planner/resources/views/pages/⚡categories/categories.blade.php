<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6 ml-2">
        <h1 class="font-bold text-md mine-text-primary">My Categories</h1>
    </div>
    <div class=" flex items-center justify-between gap-4 mb-6">
        <div class="w-full">
            <x-mine.input wire:model.live.debounce.200ms="search" placeholder="Search categories..."
                leftIcon="magnifying-glass" />
        </div>
        <x-mine.modal.trigger id="add-category-form">
            <x-mine.button type="button" class=" mine-btn-outline-primary">
                <div class="flex justify-center items-center gap-2">
                    <x-mine.icon name="plus" class="inline" />
                    <p><span class="sm:hidden">New</span><span class="hidden sm:inline">New Category</span></p>
                </div>
            </x-mine.button>
        </x-mine.modal.trigger>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @forelse ($this->categories as $category)
            <div wire:key="category-{{ $category->id }}"
                class="mine-card-interactive border-l-8
                    border-l-(--mine-category-border-left-green)
                    hover:border-l-(--mine-category-border-left-green-hover)
                    w-full flex flex-col justify-between gap-5 px-4 py-4
                    "
            >
                <div class="flex justify-between items-start">
                    <div class="flex gap-3">
                        <div class="rounded-xl size-14 mine-badge-primary flex justify-center items-center">
                            <x-mine.icon name="folder" class="size-7" />
                        </div>
                        <div class="flex flex-col justify-between py-0.5">
                            <h2 class="mine-text-primary font-medium text-[15px]">{{ $category->name }}</h2>
                            <p class="mine-text-secondary text-[13px] font-medium">{{ $category->tasks_count ?: 'No' }} {{ Str::plural('Task', $category->tasks_count) }}</p>
                        </div>
                    </div>
                    <x-mine.dropdown>
                        <x-mine.dropdown.trigger>
                            <div
                                class="mine-btn-x p-2 rounded-xl">
                                <x-mine.icon name="ellipsis-horizontal" class="size-5" />
                            </div>
                        </x-mine.dropdown.trigger>
                        <x-mine.dropdown.content>
                            <x-mine.dropdown.item>
                                <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-link hover:cursor-pointer"
                                    @click.stop="$wire.set('editing_id', {{ $category->id }}, false);
                                    $wire.set('edit_name', @js($category->name), false);
                                    $dispatch('open-modal', { id: 'edit-category-form' })"
                                >
                                    <x-mine.icon name="pencil" class="size-4" variant="solid" />
                                    <p class="text-sm font-medium">Edit</p>
                                </div>
                            </x-mine.dropdown.item>
                            <x-mine.dropdown.item destructive>
                                <div class="w-full py-2 px-4 flex items-center gap-2 mine-text-error hover:cursor-pointer"
                                    @click.stop="$wire.set('deleting_id', {{ $category->id }}, false);
                                    $dispatch('open-modal', { id: 'delete-category-confirmation' })"
                                >
                                    <x-mine.icon name="trash" class="size-4" variant="solid" />
                                    <p class="text-sm font-medium">Delete</p>
                                </div>
                            </x-mine.dropdown.item>
                        </x-mine.dropdown.content>
                    </x-mine.dropdown>

                </div>
                <div class="flex justify-center items-center">
                    <x-mine.button type="button" wire:target="" class="mine-btn-outline-primary h-10!">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium">View Tasks</p>
                            <x-mine.icon name="arrow-long-right" variant="mini" class="size-4 mt-1" />
                        </div>
                    </x-mine.button>
                </div>
            </div>
        @empty
            @if($this->search)
                <div class="col-span-full text-center py-12">
                    <x-mine.icon name="magnifying-glass" class="size-12 mx-auto mb-3 mine-text-secondary" />
                    <p class="mine-text-secondary text-sm font-medium">No categories found.</p>
                </div>
            @else
                <div class="col-span-full text-center py-12">
                    <x-mine.icon name="folder-open" class="size-12 mx-auto mb-3 mine-text-secondary" />
                    <p class="mine-text-secondary text-sm font-medium">No categories yet. Create one above.</p>
                </div>
            @endif
        @endforelse
    </div>
    <div class="mt-6 w-full">
        {{ $this->categories->links(data: ['scrollTo' => false]) }}
    </div>

    <x-mine.modal id="add-category-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Add new category</h2>
                <button type="button" @click="close(); $wire.cancelAdd()"
                    class="mine-btn-x p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($add_success === 'created')
                <div class="mb-4">
                    <x-mine.alert variant="success" title="Category created.">Your new category has been
                        added.</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Category already exists.">A category with this name already
                        exists.</x-mine.alert>
                </div>
            @endif

            @if($add_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="addCategory"
                @open-modal.window="if ($event.detail.id === 'add-category-form') { $nextTick(() => $refs.addCategoryInput?.focus()) }">
                <div>
                    <x-mine.input label="Category Name" wire:model="add_category" placeholder="Enter category name"
                        x-ref="addCategoryInput" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelAdd()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="addCategory" class="mine-btn-primary">Add</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-category-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Edit category</h2>
                <button type="button" @click="close(); $wire.cancelEdit()"
                    class="mine-btn-x p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($edit_success === 'updated')
                <div class="mb-4">
                    <x-mine.alert variant="success" title="Category updated.">Your category has been updated.</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'already_exists')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Category already exists.">A category with this name already
                        exists.</x-mine.alert>
                </div>
            @endif

            @if($edit_error === 'rate_limited')
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Too many attempts.">Please try again in a minute.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="editCategory"
                @open-modal.window="if ($event.detail.id === 'edit-category-form') { $nextTick(() => $refs.editCategoryInput?.focus()) }">
                <div>
                    <x-mine.input label="Category Name" wire:model="edit_name" placeholder="Enter category name"
                        x-ref="editCategoryInput" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelEdit()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="editCategory" class="mine-btn-primary">Save</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-category-confirmation" :close-by-clicking-away="false" :close-by-escaping="false"
        width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Delete category</h2>
                <button type="button" @click="close(); $wire.cancelDelete()"
                    class="mine-btn-x p-2 rounded-xl">
                    <x-mine.icon name="x-mark" />
                </button>
            </div>

            @if($delete_error === 'has_tasks')
                <div class="mb-4">
                    <x-mine.alert variant="danger" title="Cannot delete category.">This category has tasks. Reassign or
                        delete them first.</x-mine.alert>
                </div>
            @else
                <div class="mb-4">
                    <x-mine.alert variant="warning" title="Are you sure?">This action cannot be undone.</x-mine.alert>
                </div>
            @endif

            <form class="flex flex-col gap-4" wire:submit="deleteCategory">
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelDelete()" class="mine-btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="deleteCategory" class="mine-btn-danger">Delete</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>
