@props([
    'items' => [],
    'labelKey' => 'label',
    'series' => [],
    'height' => 300,
    'barWidth' => 50,
    'gridlines' => [1.0, 0.75, 0.5, 0.25],
    'format' => null,
])

@php
    $maxTotal = array_reduce(
        $items,
        fn (int $carry, array $item) => max(
            $carry,
            array_sum(array_map(fn (array $seriesItem) => (int) ($item[$seriesItem['key']] ?? 0), $series)),
        ),
        0,
    );

    $maxTotal = max($maxTotal, 1);

    $formatValue = fn (int $value): string => $format !== null
        ? $format($value)
        : (string) $value;

    $gridLineRows = [];
    foreach ($gridlines as $fraction) {
        $label = $formatValue((int) round($maxTotal * $fraction));

        if ($gridLineRows !== [] && end($gridLineRows)['label'] === $label) {
            continue;
        }

        $gridLineRows[] = [
            'label' => $label,
            'offset' => (int) round($fraction * $height),
        ];
    }
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <x-mine.horizontal-scroll>
        <div class="min-w-max">
            <div class="relative">
                <div class="flex items-end gap-3 pl-12 pr-2 pt-2">
                    @foreach ($items as $item)
                        @php
                            $titleParts = array_map(
                                fn (array $seriesItem) => $seriesItem['label'].': '.$formatValue((int) ($item[$seriesItem['key']] ?? 0)),
                                $series,
                            );

                            $visibleIndexes = [];
                            foreach ($series as $index => $seriesItem) {
                                if ((int) ($item[$seriesItem['key']] ?? 0) > 0) {
                                    $visibleIndexes[] = $index;
                                }
                            }

                            $firstVisible = $visibleIndexes[0] ?? null;
                            $lastVisible = $visibleIndexes === [] ? null : $visibleIndexes[count($visibleIndexes) - 1];
                        @endphp
                        <div
                            class="flex items-end shrink-0"
                            style="height: {{ $height }}px; width: {{ $barWidth }}px"
                            title="{{ implode(', ', $titleParts) }}"
                        >
                            <div class="flex w-full flex-col-reverse" style="height: {{ $height }}px">
                                @foreach ($series as $index => $seriesItem)
                                    @php
                                        $value = (int) ($item[$seriesItem['key']] ?? 0);
                                        $pixelHeight = round(($value / $maxTotal) * $height);

                                        $roundClasses = collect()
                                            ->when($index === $firstVisible, fn ($collection) => $collection->push('rounded-b-lg'))
                                            ->when($index === $lastVisible, fn ($collection) => $collection->push('rounded-t-lg'))
                                            ->implode(' ');
                                    @endphp
                                    <div
                                        class="w-full {{ $roundClasses }} {{ $seriesItem['class'] }} {{ $value > 0 ? 'min-h-0.5' : '' }}"
                                        style="height: {{ $pixelHeight }}px"
                                    ></div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pointer-events-none absolute inset-0 pr-2">
                    @foreach ($gridLineRows as $line)
                        <div
                            class="absolute left-12 right-0 border-t border-dashed border-(--mine-card-border)"
                            style="bottom: {{ $line['offset'] }}px"
                        >
                            <span
                                class="absolute -top-2.5 right-full mr-2 text-right text-[10px] font-medium mine-text-secondary whitespace-nowrap"
                            >
                                {{ $line['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pl-12 pr-2">
                @foreach ($items as $item)
                    <div class="shrink-0 text-center" style="width: {{ $barWidth }}px">
                        <span class="text-xs font-medium mine-text-secondary whitespace-nowrap">
                            {{ $item[$labelKey] ?? '' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </x-mine.horizontal-scroll>
</div>