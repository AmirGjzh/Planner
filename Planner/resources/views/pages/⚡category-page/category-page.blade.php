<div class="min-h-full bg-gradient-to-r from-slate-300 to-slate-50">
    <div class="flex flex-col md:flex-row p-4 gap-4">
        <div class="flex flex-col bg-gradient-to-r from-slate-50 to-slate-100 w-full flex-2 rounded-lg p-5">
            <x-ui.heading level="h2" size="md">Add new category</x-ui.heading>
            <x-ui.separator class="my-4"></x-ui.separator>
            <form x-data="{ text: '' }" x-on:submit.prevent="$wire.call('addCategory', text).then(() => text = '')" class="flex gap-4">
                <x-ui.field class="flex-3">
                    <x-ui.input x-model="text" type="text" placeholder="Category name" leftIcon="" />
                    <x-ui.error name="new_category" />
                </x-ui.field>
                <x-ui.button type="submit" wire:target="addCategory"
                    class="w-full flex-1 rounded-lg bg-gradient-to-r from-slate-800 to-slate-600">Add</x-ui.button>
            </form>
        </div>
        <div class="flex flex-col bg-gradient-to-r from-slate-50 to-slate-100 w-full flex-2 rounded-lg p-5">
            <x-ui.heading level="h2" size="md">Your Categories</x-ui.heading>
            <x-ui.separator class="my-4"></x-ui.separator>
            <div x-data="{ editingId: null }" class="flex flex-col gap-4">
                <x-ui.error name="delete_category" />
                @forelse ($this->categories as $category)
                    <div wire:key="category-{{ $category->id }}"
                         x-data="{ editText: '' }"
                         class="flex flex-col gap-2 p-2 shadow-md rounded-lg bg-gradient-to-r from-slate-200 to-slate-50">
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col justify-between gap-1 ml-2">
                                <x-ui.text class="font-medium text-md">{{ $category->name }}</x-ui.text>
                                <x-ui.text class="text-black/60!">{{ $category->tasks_count }} Tasks</x-ui.text>
                            </div>
                            <div class="flex flex-col justify-between gap-2">
                                <x-ui.button size="sm"
                                    x-on:click="editingId = (editingId === {{ $category->id }}) ? null : {{ $category->id }}"
                                    x-text="editingId === {{ $category->id }} ? 'Cancel' : 'Edit'"
                                    class="w-20 rounded-lg bg-gradient-to-r from-slate-800 to-slate-600">
                                </x-ui.button>
                                <x-ui.button size="sm"
                                    wire:click="deleteCategory({{ $category->id }})"
                                    class="w-20 rounded-lg bg-gradient-to-r from-red-800 to-red-600">Delete</x-ui.button>
                            </div>
                        </div>
                        <div x-show="editingId === {{ $category->id }}" x-cloak>
                            <x-ui.separator class="mt-2 mb-4 px-1"></x-ui.separator>
                            <form x-on:submit.prevent="$wire.call('editCategory', {{ $category->id }}, editText).then(() => { editText = ''; editingId = null })" class="flex gap-10">
                                <x-ui.field class="flex-1">
                                    <x-ui.input x-model="editText" type="text" placeholder="Category name" leftIcon="" />
                                    <x-ui.error name="edit_category" />
                                </x-ui.field>
                                <x-ui.button type="submit"
                                    class="w-20 rounded-lg bg-gradient-to-r from-slate-800 to-slate-600">Apply</x-ui.button>
                            </form>
                        </div>
                    </div>
                @empty
                    <x-ui.text class="text-black/50 text-center py-8">No categories yet. Create one above.</x-ui.text>
                @endforelse
                <div class="mt-4">
                    {{ $this->categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
