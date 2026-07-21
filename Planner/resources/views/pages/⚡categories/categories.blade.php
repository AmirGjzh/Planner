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
            <x-mine.button type="button" class=" btn-outline-primary">
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
                class="card-interactive w-full flex justify-between items-center sm:items-stretch sm:flex-col">
                <div class="flex gap-4 my-5 sm:mb-2 mx-4 sm:pb-4">
                    <div class="rounded-full size-13 avatar flex justify-center items-center">
                        <x-mine.icon name="folder" variant="solid" class="size-6" />
                    </div>
                    <div class="flex flex-col justify-between py-0.5">
                        <h2 class="mine-text-primary font-medium text-[15px]">{{ $category->name }}</h2>
                        <p class="text-secondary text-[13px] font-medium">{{ $category->tasks_count ?: 'No'}} {{ Str::plural('Task', $category->tasks_count) }}</p>
                    </div>
                </div>
                <div class="hidden sm:block sm:px-4 sm:opacity-60">
                    <x-mine.separator />
                </div>
                <div class="flex justify-center items-center gap-4 sm:gap-0 pr-4 sm:pr-0">
                    <div wire:key="edit-{{ $category->id }}"
                        class="w-full py-4 pl-2 flex justify-center items-center gap-2 text-[13px] font-medium text-(--mine-btn-primary-bg-hover) hover:cursor-pointer"
                        @click.stop="
                                $wire.set('editing_id', {{ $category->id }}, false);
                                $wire.set('edit_name', @js($category->name), false);
                                $dispatch('open-modal', { id: 'edit-category-form' })">
                        <x-mine.icon name="pencil-square" class="size-6 sm:size-5" />
                        <p class="hidden sm:block">Edit</p>
                    </div>
                    <div class="hidden sm:block sm:my-2 sm:mx-1 sm:opacity-60">
                        <x-mine.separator vertical />
                    </div>
                    <div wire:key="delete-{{ $category->id }}"
                        class="w-full py-4 pr-2 flex justify-center items-center gap-2 text-[13px] font-medium text-(--mine-btn-danger-bg-hover) hover:cursor-pointer"
                        @click.stop="
                                $wire.set('deleting_id', {{ $category->id }}, false);
                                $dispatch('open-modal', { id: 'delete-category-confirmation' })">
                        <x-mine.icon name="trash" class="size-6 sm:size-5" />
                        <p class="hidden sm:block">Delete</p>
                    </div>
                </div>
            </div>
        @empty
            @if($this->search)
                <div class="col-span-full text-center py-12">
                    <x-mine.icon name="magnifying-glass" class="size-12 mx-auto mb-3 text-secondary" />
                    <p class="text-secondary text-sm font-medium">No categories found.</p>
                </div>
            @else
                <div class="col-span-full text-center py-12">
                    <x-mine.icon name="folder-open" class="size-12 mx-auto mb-3 text-secondary" />
                    <p class="text-secondary text-sm font-medium">No categories yet. Create one above.</p>
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
                <button @click="close(); $wire.cancelAdd()"
                    class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
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
                    <x-mine.input label="Category Name" wire:model="new_category" placeholder="Enter category name"
                        x-ref="addCategoryInput" />
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close(); $wire.cancelAdd()" class="btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="addCategory" class="btn-primary">Add</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-category-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Edit category</h2>
                <button @click="close(); $wire.cancelEdit()"
                    class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
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
                    <x-mine.button type="button" @click="close(); $wire.cancelEdit()" class="btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="editCategory" class="btn-primary">Save</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-category-confirmation" :close-by-clicking-away="false" :close-by-escaping="false"
        width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Delete category</h2>
                <button @click="close(); $wire.cancelDelete()"
                    class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
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
                    <x-mine.button type="button" @click="close(); $wire.cancelDelete()" class="btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="deleteCategory" class="btn-danger">Delete</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>
