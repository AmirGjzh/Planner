<div class="flex-1 px-4 sm:px-8 md:px-16 pb-6 pt-4 flex flex-col">
    <div class="mb-6 ml-2">
        <h1 class="font-bold text-md mine-text-primary">My Categories</h1>
    </div>
    <div class="flex items-center justify-end mb-6">
        <x-mine.modal.trigger id="add-category-form" @click="">
            <x-mine.button
                height="h-10"
                type="button"
                class=" btn-outline-primary">
                <div class="flex justify-center items-center gap-2">
                    <x-mine.icon name="plus" class="inline"/>
                    New Category
                </div>
            </x-mine.button>
        </x-mine.modal.trigger>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @forelse ($this->categories as $category)
            <div class="card w-full flex justify-between items-center sm:items-stretch sm:flex-col sm:spt-4">
                <div class="flex gap-4 my-5 sm:mb-2 mx-4 sm:pb-4">
                    <div class="rounded-full size-13 avatar flex justify-center items-center">
                        <x-mine.icon name="folder" variant="solid" class="size-6" />
                    </div>
                    <div class="flex flex-col justify-between py-0.5">
                        <h2 class="mine-text-primary font-medium text-[15px]">{{ $category->name }}</h2>
                        <p class="text-secondary text-[13px] font-medium">{{ $category->tasks_count }} Tasks</p>
                    </div>
                </div>
                <div class="hidden sm:block px-4">
                    <x-mine.separator />
                </div>
                <div class="flex justify-center items-center gap-4 sm:gap-0 pr-4 sm:pr-0">
                    <x-mine.modal.trigger class="w-full py-4 pl-2" id="edit-category-form" @click="">
                        <div class="flex justify-center items-center gap-2 text-[13px] font-medium text-(--mine-btn-primary-bg-hover)">
                            <x-mine.icon name="pencil-square" class="inline size-6 sm:size-5"/>
                            <p class="hidden sm:block">Edit</p>
                        </div>
                    </x-mine.modal.trigger>
                    <div class="hidden sm:block my-2 mx-1">
                        <x-mine.separator vertical />
                    </div>
                    <x-mine.modal.trigger class="w-full py-4 pr-2" id="delete-category-confirmation" @click="">
                        <div class="flex justify-center items-center gap-2 text-[13px] font-medium text-(--mine-btn-danger-bg-hover)">
                            <x-mine.icon name="trash" class="inline size-6 sm:size-5"/>
                            <p class="hidden sm:block">Delete</p>
                        </div>
                    </x-mine.modal.trigger>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <x-mine.icon name="folder-open" class="size-12 mx-auto mb-3 text-secondary"/>
                <p class="text-secondary text-sm font-medium">No categories yet. Create one above.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-8 w-full flex justify-center">
        {{ $this->categories->links(data: ['scrollTo' => false]) }}
    </div>

    <x-mine.modal id="add-category-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Add new category</h2>
                <button @click="close(); "
                        class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
                    <x-mine.icon name="x-mark"/>
                </button>
            </div>

            <form class="flex flex-col gap-4" wire:submit="">
                <div>
                    <x-mine.input label="Category Name" wire:model="" placeholder="Enter category name"/>
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close();" class="btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="" class="btn-primary">Add</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="edit-category-form" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Edit category</h2>
                <button @click="close(); "
                        class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
                    <x-mine.icon name="x-mark"/>
                </button>
            </div>

            <form class="flex flex-col gap-4" wire:submit="">
                <div>
                    <x-mine.input label="Category Name" wire:model="" placeholder="Enter category name"/>
                </div>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close();" class="btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="" class="btn-primary">Save</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>

    <x-mine.modal id="delete-category-confirmation" :close-by-clicking-away="false" :close-by-escaping="false" width="lg">
        <div class="px-6 sm:px-8 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold mine-text-primary">Delete category</h2>
                <button @click="close(); "
                        class="mine-text-primary hover:bg-(--mine-btn-x-bg-hover) p-2 rounded-xl hover:cursor-pointer">
                    <x-mine.icon name="x-mark"/>
                </button>
            </div>

            <form class="flex flex-col gap-4" wire:submit="">
                <p class="text-md font-md mine-text-primary">Delete category permanantly</p>
                <div class="flex gap-4 justify-end mt-4">
                    <x-mine.button type="button" @click="close();" class="btn-ghost">
                        Cancel
                    </x-mine.button>
                    <x-mine.button wire:target="" class="btn-primary">Save</x-mine.button>
                </div>
            </form>
        </div>
    </x-mine.modal>
</div>
