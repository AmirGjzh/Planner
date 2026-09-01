@props([])

<button
    x-data="{
        get dark() {
            return document.documentElement.classList.contains('dark')
        },
        toggle() {
            document.documentElement.classList.toggle('dark')
            localStorage.setItem('theme', this.dark ? 'dark' : 'light')
        }
    }"
    x-on:click="toggle()"
    type="button"
    :aria-pressed="dark"
    aria-label="Toggle dark mode"
    {{
        $attributes->merge([
            'class' => '
                inline-flex
                size-10
                items-center
                justify-center
                rounded-xl
                text-[var(--mine-theme-switcher-color)]
                hover:bg-[var(--mine-theme-switcher-bg-hover)]
                hover:cursor-pointer
                transition-all
                duration-200
            ',
        ])
    }}
>
    <div class="relative size-5">
        <x-mine.icon
            name="Sun"
            class="absolute inset-0 transition-all duration-200 ease-out opacity-100 scale-100 dark:opacity-0 dark:scale-75"
        />
        <x-mine.icon
            name="Moon"
            class="absolute inset-0 transition-all duration-200 ease-out opacity-0 scale-75 dark:opacity-100 dark:scale-100"
        />
    </div>
</button>
