<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
@props(['dataSlot','class'])
<x-heroicons::micro.solid.check :data-slot="$dataSlot" :class="$class" >

{{ $slot ?? "" }}
</x-heroicons::micro.solid.check>