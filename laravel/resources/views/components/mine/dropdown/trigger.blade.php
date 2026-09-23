@props([
    'as' => 'button',
])

@if ($as === 'button')
    <button
        type="button"
        x-ref="trigger"
        @mouseenter="show()"
        @mouseleave="hide(150)"
        @click.stop="toggle()"
        @keydown.enter.prevent="toggle()"
        @keydown.space.prevent="toggle()"
        :aria-expanded="open"
        aria-haspopup="menu"
        {{
            $attributes->merge([
                'class' => '
            inline-flex
            items-center
            justify-center
            rounded-xl
            focus-visible:ring-4
            focus-visible:ring-(--mine-input-ring-focus)
            focus-visible:outline-none
            transition-all
            duration-200
            ease-out
            ',
            ])
        }}
    >
        {{ $slot }}
    </button>

@else
    <div
        x-ref="trigger"
        @mouseenter="show()"
        @mouseleave="hide(150)"
        @click.stop="toggle()"
        @keydown.enter.prevent="toggle()"
        @keydown.space.prevent="toggle()"
        tabindex="0"
        role="button"
        :aria-expanded="open"
        aria-haspopup="menu"
        {{
            $attributes->merge([
                'class' => '
            inline-flex
            items-center
            justify-center
            cursor-pointer
            rounded-xl
            focus-visible:ring-4
            focus-visible:ring-(--mine-input-ring-focus)
            focus-visible:outline-none
            transition-all
            duration-200
            ease-out
            flex-shrink-0
            whitespace-nowrap
            ',
            ])
        }}
    >
        {{ $slot }}
    </div>

@endif
