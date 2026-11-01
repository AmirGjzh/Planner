<?php

use App\Actions\Reports\ReportsAction;
use App\Livewire\Concerns\HasUser;
use App\Support\Jalali;
use App\Support\Minutes;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app', ['title' => 'Reports'])] class extends Component
{
    use HasUser;

    /**
     * The presets that own server-side calendar bounds. `custom` is the only
     * other UI value and carries its own client-written range.
     */
    private const BOUNDED_PRESETS = ['this_week', 'last_week', 'this_month', 'last_month'];

    public string $preset = 'this_week';

    public ?array $range_filter = null;

    public function mount(): void
    {
        if ($toast = session()->pull('toast')) {
            $this->dispatch('toast', ...$toast);
        }

        $this->range_filter = $this->presetRange('this_week');
    }

    public function selectPreset(string $preset): void
    {
        if (! in_array($preset, [...self::BOUNDED_PRESETS, 'custom'], true)) {
            return;
        }

        $this->preset = $preset;

        if ($preset !== 'custom') {
            $this->range_filter = $this->presetRange($preset);
        }
    }

    #[Computed]
    public function stats(): array
    {
        $range = $this->range();

        return app(ReportsAction::class)->summary($this->user, $range['start'], $range['end']);
    }

    /**
     * The Action returns raw `Y-m-d` days; labels are display copy, so the
     * locale split (Jalali `d MMM` in fa, Gregorian `M j` in en) lives here
     * and the bar-chart only ever sees finished rows.
     */
    #[Computed]
    public function chart(): array
    {
        $range = $this->range();

        return array_map(
            fn (array $row) => [
                'label' => $this->dayLabel($row['day']),
                'completed' => $row['completed'],
                'remaining' => $row['remaining'],
            ],
            app(ReportsAction::class)->chart($this->user, $range['start'], $range['end']),
        );
    }

    private function dayLabel(string $day): string
    {
        $date = Carbon::parse($day);

        return app()->isLocale('fa')
            ? Jalali::format($date, 'd MMM')
            : $date->format('M j');
    }

    /**
     * `range_filter` is written straight from the calendar on the client, so
     * the pair is only trusted once it round-trips as two real dates. Anything
     * else — the mid-selection `end: null`, a tampered payload — resolves to
     * the active preset's bounds instead of reaching the Action as a malformed
     * range (which would TypeError on the `string` parameters).
     *
     * @return array{start: string, end: string}
     */
    private function range(): array
    {
        $range = $this->range_filter;

        if (is_array($range) && $this->isDate($range['start'] ?? null) && $this->isDate($range['end'] ?? null)) {
            return ['start' => $range['start'], 'end' => $range['end']];
        }

        return $this->presetRange($this->preset);
    }

    /**
     * Bounds for a bounded preset. The preset itself is client-writable public
     * state, so anything outside the four (`custom` has no server bounds, nor
     * does a tampered value) falls back to this week — keeping the `match`
     * arms below total and the page renderable on every payload.
     *
     * @return array{start: string, end: string}
     */
    private function presetRange(string $preset): array
    {
        if (! in_array($preset, self::BOUNDED_PRESETS, true)) {
            $preset = 'this_week';
        }

        $today = now();

        if (app()->isLocale('fa')) {
            return match ($preset) {
                'this_week' => Jalali::weekBounds($today),
                'last_week' => Jalali::weekBounds($today->copy()->subWeek()),
                'this_month' => Jalali::monthBounds($today),
                'last_month' => Jalali::monthBounds($today->copy()->subMonth()),
            };
        }

        return match ($preset) {
            'this_week' => [
                'start' => $today->copy()->startOfWeek(Carbon::SUNDAY)->toDateString(),
                'end' => $today->copy()->startOfWeek(Carbon::SUNDAY)->addDays(6)->toDateString(),
            ],
            'last_week' => [
                'start' => $today->copy()->subWeek()->startOfWeek(Carbon::SUNDAY)->toDateString(),
                'end' => $today->copy()->subWeek()->startOfWeek(Carbon::SUNDAY)->addDays(6)->toDateString(),
            ],
            'this_month' => [
                'start' => $today->copy()->startOfMonth()->toDateString(),
                'end' => $today->copy()->endOfMonth()->toDateString(),
            ],
            'last_month' => [
                'start' => $today->copy()->subMonth()->startOfMonth()->toDateString(),
                'end' => $today->copy()->subMonth()->endOfMonth()->toDateString(),
            ],
        };
    }

    /**
     * A date string the queries can use as-is: `Y-m-d` shape that round-trips
     * through a real parse, so rolled-over values (`2025-02-31`) and anything
     * non-date never reach the range filters.
     */
    private function isDate(mixed $value): bool
    {
        if (! is_string($value) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
            return false;
        }

        $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $parsed !== false && $parsed->format('Y-m-d') === $value;
    }

    public function formatMinutes(int $minutes): string
    {
        return Minutes::format($minutes);
    }
};
