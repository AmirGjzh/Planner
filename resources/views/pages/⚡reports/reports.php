<?php

use App\Actions\Reports\ReportsAction;
use App\Livewire\Concerns\HasUser;
use App\Support\Minutes;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use HasUser;

    public string $preset = 'this_week';

    public ?array $range_filter = null;

    public function mount(): void
    {
        $this->range_filter = $this->presetRange('this_week');
    }

    public function selectPreset(string $preset): void
    {
        if (! in_array($preset, ['this_week', 'last_week', 'this_month', 'last_month', 'custom'], true)) {
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

    #[Computed]
    public function chart(): array
    {
        $range = $this->range();

        return app(ReportsAction::class)->chart($this->user, $range['start'], $range['end']);
    }

    /**
     * @return array{start: string, end: string}
     */
    private function range(): array
    {
        return $this->range_filter ?? $this->presetRange('this_week');
    }

    private function presetRange(string $preset): array
    {
        $today = now();

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

    public function formatMinutes(int $minutes): string
    {
        return Minutes::format($minutes);
    }
};
